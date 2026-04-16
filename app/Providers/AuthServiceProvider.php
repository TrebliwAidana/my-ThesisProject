<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * We wire every permission slug in the DB to a Gate so that
     * Gate::allows('documents.view') and $user->hasPermission('documents.view')
     * are always in sync.
     *
     * Level-1 (System Administrator) automatically passes every Gate
     * because hasPermission() returns true unconditionally for level 1.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Guard against running before migrations exist (e.g. during fresh install)
        if (!$this->permissionsTableExists()) {
            return;
        }

        // Register a Gate for every permission slug in the database.
        // without changing any existing controller logic.
        Permission::all()->each(function (Permission $permission) {
            Gate::define($permission->slug, function ($user) use ($permission) {
                return $user->hasPermission($permission->slug);
            });
        });
    }

    /**
     * Safely check if the permissions table exists before querying it.
     * Prevents crashes during php artisan migrate on a fresh install.
     */
    private function permissionsTableExists(): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasTable('permissions');
        } catch (\Exception $e) {
            return false;
        }
    }
}