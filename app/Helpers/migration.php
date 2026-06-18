<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (! function_exists('migration_create_table')) {
    /**
     * Create a table only when it is missing (safe for partial live databases).
     */
    function migration_create_table(string $table, callable $callback): void
    {
        if (! Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }
}

if (! function_exists('migration_seed_if_empty')) {
    /**
     * Run a seed callback only when the table has no rows.
     */
    function migration_seed_if_empty(string $table, callable $callback): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (\Illuminate\Support\Facades\DB::table($table)->exists()) {
            return;
        }

        $callback();
    }
}

if (! function_exists('migration_add_column_if_missing')) {
    /**
     * Add a column only when the table exists and the column is missing.
     */
    function migration_add_column_if_missing(string $table, string $column, callable $callback): void
    {
        if (! Schema::hasTable($table) || Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, $callback);
    }
}

if (! function_exists('migration_table_if_exists')) {
    /**
     * Run a schema change only when the target table exists.
     */
    function migration_table_if_exists(string $table, callable $callback): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, $callback);
    }
}
