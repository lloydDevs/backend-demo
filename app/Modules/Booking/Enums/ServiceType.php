<?php

namespace App\Modules\Booking\Enums;

enum ServiceType: string
{
    case ConferenceRoomA = 'conference_room_a';
    case ConferenceRoomB = 'conference_room_b';
    case Massage60 = 'massage_60min';
    case Massage90 = 'massage_90min';
    case HaircutMen = 'haircut_men';
    case HaircutWomen = 'haircut_women';

    public function label(): string
    {
        return match ($this) {
            self::ConferenceRoomA => 'Conference Room A',
            self::ConferenceRoomB => 'Conference Room B',
            self::Massage60 => 'Massage - 60 Minutes',
            self::Massage90 => 'Massage - 90 Minutes',
            self::HaircutMen => "Men's Haircut",
            self::HaircutWomen => "Women's Haircut",
        };
    }
}
