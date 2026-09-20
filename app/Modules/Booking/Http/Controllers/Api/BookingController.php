<?php

namespace App\Modules\Booking\Http\Controllers\Api;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Http\Requests\StoreBookingRequest;
use App\Modules\Booking\Http\Requests\TransitionBookingStatusRequest;
use App\Modules\Booking\Http\Requests\UpdateBookingRequest;
use App\Modules\Booking\Http\Resources\BookingResource;
use App\Modules\Booking\Models\Booking;
use App\Modules\Booking\Services\BookingService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class BookingController
{
    public function __construct(private BookingService $service) {}

    public function index(): AnonymousResourceCollection
    {
        $status = request('status') ? BookingStatus::tryFrom(request('status')) : null;
        if (request('status') && ! $status) {
            throw ValidationException::withMessages(['status' => ['The selected status is invalid.']]);
        }
        return BookingResource::collection($this->service->list($status));
    }

    public function store(StoreBookingRequest $request)
    {
        return (new BookingResource($this->service->createBooking($request->validated())->load('customer')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Booking $booking): BookingResource
    {
        return new BookingResource(
            Booking::query()->with('customer')->findOrFail($booking->id),
        );
    }

    public function update(UpdateBookingRequest $request, Booking $booking): BookingResource
    {
        $updated = $this->service->updateBooking($booking->id, $request->validated());

        return new BookingResource($updated->load('customer'));
    }

    public function destroy(Booking $booking): array
    {
        $this->service->delete($booking->id);
        return ['message' => 'Booking deleted successfully.'];
    }

    public function updateStatus(TransitionBookingStatusRequest $request, Booking $booking): BookingResource
    {
        $booking = Booking::query()->with('customer')->findOrFail($booking->id);

        return new BookingResource($this->service->transition($booking, BookingStatus::from($request->validated('status')))->load('customer'));
    }
}
