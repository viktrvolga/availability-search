<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;

use App\Product\Product;

final class LocationSearchResponse implements \Countable
{
    /** @var Product[] */
    public array $products;

    public function count(): int
    {
        return \count($this->products);
    }

    private function __construct()
    {
    }
}
