<?php

declare(strict_types=1);

namespace App\Gpt;

use App\Common\Structures\City;
use App\Common\Structures\DateTimeRange;

final readonly class ReservationRequest
{
    public function __construct(
        public City          $city,
        public int           $personsCount,
        public DateTimeRange $dateTimeRange
    )
    {
    }
}
