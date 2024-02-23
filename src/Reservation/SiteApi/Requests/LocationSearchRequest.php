<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;

use App\Common\Structures\Coordinates;

final readonly class LocationSearchRequest implements SiteApiRequest
{
    private const DEFAULT_PER_PAGE = 50;
    private const DEFAULT_TAXON_ID = 211;

    public function __construct(private int $page, private Coordinates $coordinates)
    {
    }

    public function url(): string
    {
        return 'https://app.actievandedag.nl/api/v2/shop/products/location-search';
    }

    public function method(): string
    {
        return 'GET';
    }

    public function parameters(): array
    {
        return [
            'query' => [
                'page' => $this->page,
                'itemsPerPage' => self::DEFAULT_PER_PAGE,
                'latitude' => $this->coordinates->latitude,
                'longitude' => $this->coordinates->longitude,
                'taxonId' => self::DEFAULT_TAXON_ID
            ]
        ];
    }

    public function responseStructure(): string
    {
        return LocationSearchResponse::class;
    }
}
