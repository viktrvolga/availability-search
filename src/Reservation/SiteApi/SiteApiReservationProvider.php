<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi;

use App\Common\Structures\City;
use App\Common\Structures\DateTimeRange;
use App\Product\Availability\ProductAvailabilityAnyDate;
use App\Product\Availability\ProductAvailabilitySpecificSlots;
use App\Product\Product;
use App\Reservation\ReservationProvider;
use App\Reservation\SiteApi\Requests\LocationAvailabilityRequest;
use App\Reservation\SiteApi\Requests\LocationAvailabilityResponse;
use App\Reservation\SiteApi\Requests\LocationSearchRequest;
use App\Reservation\SiteApi\Requests\LocationSearchResponse;
use App\Reservation\SiteApi\Requests\LocationTimeslotsRequest;
use App\Reservation\SiteApi\Requests\LocationTimeslotsResponse;

final readonly class SiteApiReservationProvider implements ReservationProvider
{
    public function __construct(private SiteApiClient $client)
    {
    }

    public function search(City $in, DateTimeRange $range, int $personsCount, int $limit = 10): \Generator
    {
        $currentEntries = 0;

        /** @var Product $product */
        foreach ($this->fetchProducts($in, $range, $personsCount) as $product) {
            if (++$currentEntries <= $limit) {
                yield $product;
            } else {
                break;
            }
        }
    }

    private function fetchProducts(City $in, DateTimeRange $range, int $personsCount): \Generator
    {
        $currentPage = 0;

        do {
            /** @var LocationSearchResponse $response */
            $response = $this->client->handle(
                new LocationSearchRequest(
                    page: ++$currentPage,
                    coordinates: $in->coordinates
                )
            );

            if ($response->count() !== 0) {
                foreach ($response->products as $product) {
                    if ($product->defaultVariant === null) {
                        continue;
                    }

                    if ($product->defaultVariant->isReservationDeal) {
                        $availability = $this->fetchAvailability(
                            restaurantId: $product->defaultVariant->id,
                            personsCount: $personsCount,
                            range: $range
                        );

                        if (\count($availability) > 0) {
                            yield $product->withNewAvailability(new ProductAvailabilitySpecificSlots($availability));
                        }
                    } else {
                        yield $product->withNewAvailability(new ProductAvailabilityAnyDate());
                    }
                }
            } else {
                break;
            }
        } while (true);
    }

    private function fetchAvailability(int $restaurantId, int $personsCount, DateTimeRange $range): array
    {
        try {
            $timeslots = [];
            $expectedDates = \array_filter([$range->from, $range->isSame() === false ? $range->to : null]);

            foreach ($expectedDates as $expectedDate) {
                $isAvailable = $this->isAvailableOnSpecificDate(
                    restaurantId: $restaurantId,
                    personsCount: $personsCount,
                    datetime: $expectedDate
                );

                if ($isAvailable) {
                    $availableDates = $this->fetchTimeslots(
                        restaurantId: $restaurantId,
                        personsCount: $personsCount,
                        datetime: $expectedDate
                    );

                    foreach ($availableDates as $availableDate) {
                        if ($range->from <= $availableDate && $range->to >= $availableDate) {
                            $timeslots[] = $availableDate;
                        }
                    }
                }
            }

            return $timeslots;
        } catch (\Throwable) {
            return [];
        }
    }

    private function isAvailableOnSpecificDate(int $restaurantId, int $personsCount, \DateTimeImmutable $datetime): bool
    {
        /** @var LocationAvailabilityResponse $result */
        $result = $this->client->handle(
            new LocationAvailabilityRequest(
                id: $restaurantId,
                personsCount: $personsCount,
                datetime: $datetime
            )
        );

        return $result->contains($datetime);
    }

    /** @return \DateTimeImmutable[] */
    private function fetchTimeslots(int $restaurantId, int $personsCount, \DateTimeImmutable $datetime): array
    {
        /** @var LocationTimeslotsResponse $result */
        $result = $this->client->handle(
            new LocationTimeslotsRequest(
                id: $restaurantId,
                personsCount: $personsCount,
                datetime: $datetime
            )
        );

        return \array_map(
            static function (string $timeslot) use ($datetime): \DateTimeImmutable {
                return \DateTimeImmutable::createFromFormat(
                    'Y-m-d H:i:s',
                    \sprintf('%s %s', $datetime->format('Y-m-d'), $timeslot)
                );
            },
            $result->timeslots
        );
    }
}
