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

        // ✅ Fixed: check both table AND column exist before querying
        try {
            if (
                !app()->runningInConsole() &&
                Schema::hasTable('roles') &&
                Schema::hasColumn('roles', 'deleted_at')
            ) {
                Cache::remember('roles_with_perms', 3600, function () {
                    return Role::with('permissions')->get();
                });
            }
        } catch (\Exception $e) {
            // Silently fail during deployment/migration
        }
    }

    public function register(): void
    {
        //
    }
}