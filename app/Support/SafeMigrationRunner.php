<?php

namespace App\Support;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SafeMigrationRunner
{
    /**
     * @return array{log: array<int, string>, remaining: int}
     */
    public static function run(int $limit = 0): array
    {
        return (new self)->execute($limit);
    }

    /**
     * @return array{log: array<int, string>, remaining: int}
     */
    public function execute(int $limit = 0): array
    {
        /** @var Migrator $migrator */
        $migrator = app('migrator');
        /** @var MigrationRepositoryInterface $repository */
        $repository = app('migration.repository');

        if (! Schema::hasTable('migrations')) {
            Artisan::call('migrate:install');
        }

        $files = $migrator->getMigrationFiles([database_path('migrations')]);
        $ran = $repository->getRan();

        $pending = collect($files)
            ->reject(fn (string $file) => in_array($migrator->getMigrationName($file), $ran, true))
            ->values()
            ->all();

        if ($pending === []) {
            return ['log' => ['Nothing to migrate.'], 'remaining' => 0];
        }

        $log = [];
        $batch = $repository->getNextBatchNumber();

        [$pending, $preMarked] = $this->preMarkMigrationsWithExistingTables($repository, $migrator, $pending, $batch);
        $log = array_merge($log, $preMarked);

        if ($pending === []) {
            return ['log' => $log, 'remaining' => 0];
        }

        if ($limit > 0) {
            $pending = array_slice($pending, 0, $limit);
        }

        foreach ($pending as $file) {
            $name = $migrator->getMigrationName($file);

            try {
                $migrator->runPending([$file]);
                $log[] = "Ran: {$name}";
            } catch (Throwable $e) {
                if ($this->shouldMarkAsAlreadyApplied($e)) {
                    $this->markMigrationRan($repository, $name, $batch);
                    $log[] = "Skipped (already on database): {$name}";
                    continue;
                }

                $log[] = "Failed: {$name}";
                throw $e;
            }
        }

        $remaining = count(collect($files)
            ->reject(fn (string $file) => in_array($migrator->getMigrationName($file), $repository->getRan(), true))
            ->values()
            ->all());

        return ['log' => $log, 'remaining' => $remaining];
    }

    /**
     * @param  array<int, string>  $pending
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function preMarkMigrationsWithExistingTables(
        MigrationRepositoryInterface $repository,
        Migrator $migrator,
        array $pending,
        int $batch,
    ): array {
        $stillPending = [];
        $log = [];

        foreach ($pending as $file) {
            $name = $migrator->getMigrationName($file);
            $tables = $this->extractCreateTables($file);

            if ($tables !== [] && $this->allTablesExist($tables)) {
                $this->markMigrationRan($repository, $name, $batch);
                $log[] = 'Skipped (tables already present): '.$name;
                continue;
            }

            $stillPending[] = $file;
        }

        return [$stillPending, $log];
    }

    /**
     * @return array<int, string>
     */
    private function extractCreateTables(string $file): array
    {
        $contents = @file_get_contents($file);

        if ($contents === false) {
            return [];
        }

        preg_match_all(
            "/(?:Schema::create|migration_create_table)\(\s*['\"]([a-zA-Z0-9_]+)['\"]/",
            $contents,
            $matches
        );

        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * @param  array<int, string>  $tables
     */
    private function allTablesExist(array $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    private function markMigrationRan(MigrationRepositoryInterface $repository, string $name, int $batch): void
    {
        if (! in_array($name, $repository->getRan(), true)) {
            $repository->log($name, $batch);
        }
    }

    private function shouldMarkAsAlreadyApplied(Throwable $e): bool
    {
        $messages = [$e->getMessage()];

        if ($e instanceof QueryException && $e->getPrevious()) {
            $messages[] = $e->getPrevious()->getMessage();
        }

        $haystack = strtolower(implode(' ', $messages));

        foreach ([
            'already exists',
            'duplicate column',
            'duplicate key name',
            'duplicate entry',
            '1050',
            '1060',
            '1061',
            '1062',
        ] as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
