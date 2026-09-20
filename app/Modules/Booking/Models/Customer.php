<?php

namespace App\Modules\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email'];

    protected static function newFactory()
    {
        return \App\Modules\Booking\Database\Factories\CustomerFactory::new();
    }

    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
}
