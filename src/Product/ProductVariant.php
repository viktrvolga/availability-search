<?php

declare(strict_types=1);

namespace App\Product;

final readonly class ProductVariant
{
    public function __construct(
        public int    $id,
        public string $name,
        public string $code,
        public bool   $isSellable,
        public int    $price,
        public int    $originalPrice,
        public string $quantityLabel,
        public bool   $isMergedVouchersDeal,
        public bool   $isReservationDeal,
        public bool   $isCallOnlyDeal,
        public int    $availableStock
    )
    {
    }
}
