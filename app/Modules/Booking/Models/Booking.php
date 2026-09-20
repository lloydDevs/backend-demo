<?php

namespace App\Modules\Booking\Models;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'service_name',
        'booking_date', 'booking_time', 'status', 'notes',
    ];

    protected $casts = [
        'booking_date' => 'date:Y-m-d',
        'booking_time' => 'datetime:H:i',
        'status' => BookingStatus::class,
        'service_name' => ServiceType::class,
    ];

    protected static function newFactory()
    {
        return \App\Modules\Booking\Database\Factories\BookingFactory::new();
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
}
