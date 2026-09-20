<?php

namespace App\Modules\Booking\Http\Requests;

use App\Modules\Booking\Enums\BookingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TransitionBookingStatusRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['status' => ['required', new Enum(BookingStatus::class)]]; }
}
