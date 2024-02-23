<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;

final class LocationTimeslotsResponse
{
    public string $date;

    /** @var string[] */
    public array $timeslots;

    private function __construct()
    {
    }
}
