<?php

use App\Modules\Auth\Providers\AuthModuleServiceProvider;
use App\Modules\Product\Providers\ProductModuleServiceProvider;
use App\Modules\Role\Providers\RoleModuleServiceProvider;
use App\Providers\AppServiceProvider;
use Laravel\Fortify\FortifyServiceProvider;

return [
    AppServiceProvider::class,

    // Third-party
    FortifyServiceProvider::class,

    // Modules — add each new module's provider here (manual modular registration,
    // no `module:make` command involved).
    RoleModuleServiceProvider::class,
    AuthModuleServiceProvider::class,
    ProductModuleServiceProvider::class,
];
