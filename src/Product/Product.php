<?php

declare(strict_types=1);

namespace App\Product;

use App\Common\Structures\Address;
use App\Common\Structures\Image;
use App\Product\Availability\ProductAvailability;

final readonly class Product
{
    /**
     * @param int $id
     * @param string $name
     * @param Address[] $addresses
     * @param Image[] $images
     * @param ?bool $hasExternalBooking
     * @param int $price
     * @param int $originalPrice
     * @param ProductVariant|null $defaultVariant
     * @param ProductAvailability|null $availability
     */
    public function __construct(
        public int                  $id,
        public string               $name,
        public array                $addresses,
        public array                $images,
        public bool                 $hasExternalBooking,
        public int                  $price,
        public int                  $originalPrice,
        public ?ProductVariant      $defaultVariant,
        public ?ProductAvailability $availability = null
    )
    {
    }

    public function withNewAvailability(ProductAvailability $availability): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            addresses: $this->addresses,
            images: $this->images,
            hasExternalBooking: $this->hasExternalBooking,
            price: $this->price,
            originalPrice: $this->originalPrice,
            defaultVariant: $this->defaultVariant,
            availability: $availability
        );
    }
}
