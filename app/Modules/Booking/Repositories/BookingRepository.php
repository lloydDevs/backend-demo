<?php

namespace App\Modules\Booking\Repositories;

use App\Modules\Booking\Models\Booking;
use App\Modules\Core\Repositories\BaseRepository;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }
}
