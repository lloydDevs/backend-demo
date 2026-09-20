<?php

namespace App\Modules\Booking\Providers;

use App\Modules\Booking\Repositories\BookingRepository;
use App\Modules\Booking\Repositories\BookingRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class BookingModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
