<?php

declare(strict_types=1);

namespace App\Reservation;

use App\Common\Structures\City;
use App\Common\Structures\DateTimeRange;
use App\Product\Product;

interface ReservationProvider
{
    /**
     * @param City $in
     * @param DateTimeRange $range
     * @param int $personsCount
     * @param int $limit
     *
     * @return \Generator<Product>
     */
    public function search(City $in, DateTimeRange $range, int $personsCount, int $limit = 10): \Generator;
}
