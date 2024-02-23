<?php

declare(strict_types=1);

namespace App\Common\Structures;

final readonly class Address
{
    public function __construct(
        public string  $street,
        public int     $houseNumber,
        public string  $postcode,
        public string  $city,
        public ?string $houseNumberExtension
    )
    {
    }
}
