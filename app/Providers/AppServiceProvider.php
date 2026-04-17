<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Services\ThemeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $view->with('theme', app(ThemeService::class)->getTheme());
        });

        if (!app()->runningInConsole() || Schema::hasTable('roles')) {
            Cache::remember('roles_with_perms', 3600, function () {
                return Role::with('permissions')->get();
            });
        }
    }

    public function register(): void
    {
        //
    }
}