<?php

declare(strict_types=1);

namespace App\Common\Structures;

final readonly class City
{
    public function __construct(
        public string      $name,
        public Coordinates $coordinates
    )
    {
    }
}
