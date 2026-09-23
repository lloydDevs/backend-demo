<?php

namespace App\Modules\Booking\Http\Controllers\Api;

use App\Modules\Booking\Http\Requests\StoreCustomerRequest;
use App\Modules\Booking\Http\Requests\UpdateCustomerRequest;
use App\Modules\Booking\Http\Resources\CustomerResource;
use App\Modules\Booking\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController
{
    public function index(): AnonymousResourceCollection
    {
        $allowedSorts = ['id', 'name', 'email', 'created_at'];
        $sortBy = request('sort_by', request('sort', 'id'));
        $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'id';

        $sortDir = strtolower(request('sort_dir', request('order', 'asc'))) === 'desc' ? 'desc' : 'asc';

        return CustomerResource::collection(Customer::query()->orderBy($sortBy, $sortDir)->get());
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::query()->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer->fresh());
    }

    public function destroy(Customer $customer): array
    {
        $customer->delete();

        return ['message' => 'Customer deleted successfully.'];
    }
}

