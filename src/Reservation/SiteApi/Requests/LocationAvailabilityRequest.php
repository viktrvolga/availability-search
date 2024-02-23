<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;

final readonly class LocationAvailabilityRequest implements SiteApiRequest
{
    public function __construct(
        private int                $id,
        private int                $personsCount,
        private \DateTimeImmutable $datetime
    )
    {
    }

    public function url(): string
    {
        return \sprintf('https://app.actievandedag.nl/api/v2/shop/reservation/availability/%d', $this->id);
    }

    public function method(): string
    {
        return 'GET';
    }

    public function parameters(): array
    {
        return [
            'query' => [
                'persons' => $this->personsCount,
                'date' => $this->datetime
            ]
        ];
    }

    public function responseStructure(): string
    {
        return LocationAvailabilityResponse::class;
    }
}
