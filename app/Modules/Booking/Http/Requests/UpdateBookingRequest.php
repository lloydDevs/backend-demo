<?php

namespace App\Modules\Booking\Http\Requests;

use App\Modules\Booking\Enums\ServiceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'integer', 'exists:customers,id'],
            'service_name' => ['sometimes', new Enum(ServiceType::class)],
            'booking_date' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:today'],
            'booking_time' => ['sometimes', 'date_format:H:i'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }
}
