<?php

namespace App\Modules\Booking\Http\Controllers\Api;

use App\Modules\Booking\Enums\ServiceType;

class ServiceController
{
    public function index(): array
    {
        return [
            'data' => array_map(
                fn (ServiceType $service) => [
                    'value' => $service->value,
                    'label' => $service->label(),
                ],
                ServiceType::cases(),
            ),
        ];
    }
}
