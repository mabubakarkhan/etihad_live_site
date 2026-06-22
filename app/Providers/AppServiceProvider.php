<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Local\LocalFilesystemAdapter as FlysystemLocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;
use ReflectionMethod;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach (glob(app_path('Helpers/*.php')) ?: [] as $helperFile) {
            require_once $helperFile;
        }

        $this->applyDatabaseConfigFromDotEnvWhenLocal();
    }

    /**
     * Windows/XAMPP may inject DB_* into the process env (e.g. pgsql/erp_dev_1).
     * Laravel's .env loader will not override those — read .env directly for local dev.
     */
    protected function applyDatabaseConfigFromDotEnvWhenLocal(): void
    {
        if ($this->app->configurationIsCached() || ! $this->app->environment('local')) {
            return;
        }

        $path = base_path('.env');
        if (! is_readable($path)) {
            return;
        }

        $keys = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
        $fromFile = [];
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            if (in_array($name, $keys, true)) {
                $fromFile[$name] = trim($value, " \t\n\r\0\x0B'\"");
            }
        }

        if (empty($fromFile['DB_CONNECTION'])) {
            return;
        }

        $connection = $fromFile['DB_CONNECTION'];
        config(['database.default' => $connection]);

        $connectionConfig = config("database.connections.{$connection}");
        if (! is_array($connectionConfig)) {
            return;
        }

        $map = [
            'DB_HOST' => 'host',
            'DB_PORT' => 'port',
            'DB_DATABASE' => 'database',
            'DB_USERNAME' => 'username',
            'DB_PASSWORD' => 'password',
        ];
        foreach ($map as $envKey => $configKey) {
            if (array_key_exists($envKey, $fromFile)) {
                $connectionConfig[$configKey] = $fromFile[$envKey];
            }
        }

        config(["database.connections.{$connection}" => $connectionConfig]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureFilesystemWithoutFileinfo();
        $this->registerAdminViewComposer();
        $this->registerFrontViewComposer();
    }

    /**
     * Hosts without PHP fileinfo break Flysystem's default FinfoMimeTypeDetector.
     */
    protected function configureFilesystemWithoutFileinfo(): void
    {
        if (extension_loaded('fileinfo') && class_exists(\finfo::class, false)) {
            return;
        }

        Storage::extend('local', function ($app, array $config) {
            /** @var FilesystemManager $manager */
            $manager = $app->make('filesystem');

            $visibility = PortableVisibilityConverter::fromArray(
                $config['permissions'] ?? [],
                $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
            );

            $links = ($config['links'] ?? null) === 'skip'
                ? FlysystemLocalAdapter::SKIP_LINKS
                : FlysystemLocalAdapter::DISALLOW_LINKS;

            $adapter = new FlysystemLocalAdapter(
                $config['root'],
                $visibility,
                $config['lock'] ?? LOCK_EX,
                $links,
                new ExtensionMimeTypeDetector()
            );

            $createFlysystem = new ReflectionMethod($manager, 'createFlysystem');
            $createFlysystem->setAccessible(true);
            $driver = $createFlysystem->invoke($manager, $adapter, $config);

            $filesystem = new LocalFilesystemAdapter($driver, $adapter, $config);

            if (! empty($config['serve'])) {
                $filesystem->shouldServeSignedUrls(
                    true,
                    fn () => $app['url'],
                );
            }

            return $filesystem;
        });
    }

    /**
     * Share current admin user with all admin views (e.g. sidebar "Signed in as").
     */
    protected function registerAdminViewComposer(): void
    {
        View::composer('admin.*', function ($view) {
            $view->with('adminUser', admin_user());
            $view->with('adminUnreadNotificationCount', \App\Models\AdminNotification::unread()->count());
        });
    }

    /**
     * Share wishlist count with front header on every page.
     */
    protected function registerFrontViewComposer(): void
    {
        View::composer('partials.header', function ($view) {
            $raw = request()->cookie('etihad_wishlist');
            $count = 0;
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode(urldecode($raw), true);
                if (is_array($decoded)) {
                    $count = count($decoded);
                }
            }
            $view->with('wishlistCount', $count);
        });
    }
}
