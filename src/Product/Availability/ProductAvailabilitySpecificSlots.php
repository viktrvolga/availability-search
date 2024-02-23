<?php

namespace App\Product\Availability;

final readonly class ProductAvailabilitySpecificSlots implements ProductAvailability
{
    public function __construct(public array $timeslots)
    {
    }
}
