<?php

namespace App\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

class RoleModuleServiceProvider extends ServiceProvider
{
    /**
     * This module relies on spatie/laravel-permission for its core
     * roles/permissions tables (published via `vendor:publish` into
     * database/migrations). This provider is the place to add any
     * module-specific bindings, gates, or Blade/Inertia helpers later.
     */
    public function boot(): void
    {
        //
    }
}
