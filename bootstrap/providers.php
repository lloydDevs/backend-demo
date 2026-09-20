<?php

use App\Modules\Booking\Providers\BookingModuleServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,

    // Manual module registration.
    BookingModuleServiceProvider::class,
];
