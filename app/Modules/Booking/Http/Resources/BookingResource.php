<?php

namespace App\Modules\Booking\Http\Resources;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Enums\ServiceType;
use App\Modules\Booking\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = Customer::query()->findOrFail($this->customer_id);
        $service = $this->service_name ?? ServiceType::from($this->getRawOriginal('service_name'));
        $status = $this->status ?? BookingStatus::from($this->getRawOriginal('status') ?? BookingStatus::Pending->value);

        return [
            'id' => $this->id,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'service_name' => $service->value,
            'service_label' => $service->label(),
            'booking_date' => $this->booking_date->format('Y-m-d'),
            'booking_time' => $this->booking_time->format('H:i'),
            'status' => $status->value,
            'status_label' => $status->label(),
            'status_color' => $status->color(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
