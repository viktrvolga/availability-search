<?php

declare(strict_types=1);

namespace App\Common\Structures;

final readonly class Coordinates
{
    public function __construct(
        public string $latitude,
        public string $longitude
    )
    {
    }
}
