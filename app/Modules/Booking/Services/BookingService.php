<?php

namespace App\Modules\Booking\Services;

use App\Modules\Booking\Enums\BookingStatus;
use App\Modules\Booking\Repositories\BookingRepositoryInterface;
use App\Modules\Core\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class BookingService extends BaseService
{
    public function __construct(BookingRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function list(?BookingStatus $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = \App\Modules\Booking\Models\Booking::query()->with('customer')->latest();
        if ($status) $query->where('status', $status->value);
        return $query->paginate($perPage);
    }

    public function createBooking(array $data)
    {
        $data['status'] = BookingStatus::Pending;
        return $this->create($data);
    }

    public function updateBooking(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    public function transition($booking, BookingStatus $status)
    {
        if (! $booking->status->canTransitionTo($status)) {
            throw ValidationException::withMessages([
                'status' => ["Cannot transition from '{$booking->status->value}' to '{$status->value}'."],
            ]);
        }

        $booking->update(['status' => $status]);
        return $booking->fresh();
    }
}
