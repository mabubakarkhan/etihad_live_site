<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerAdminViewComposer();
        $this->registerFrontViewComposer();
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
