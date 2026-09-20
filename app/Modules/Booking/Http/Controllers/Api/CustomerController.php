<?php

namespace App\Modules\Booking\Http\Controllers\Api;

use App\Modules\Booking\Http\Resources\CustomerResource;
use App\Modules\Booking\Models\Customer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController
{
    public function index(): AnonymousResourceCollection
    {
        return CustomerResource::collection(Customer::query()->orderBy('name')->get());
    }
}
