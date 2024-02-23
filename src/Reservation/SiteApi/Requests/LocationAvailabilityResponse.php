<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;


final class LocationAvailabilityResponse implements \IteratorAggregate
{
    public array $collection;

    /**
     * @return \Generator<\DateTimeImmutable>
     */
    public function getIterator(): \Generator
    {
        yield from \array_map(
            static function (array $entry): \DateTimeImmutable {
                return \DateTimeImmutable::createFromFormat('Y-m-d', $entry['date']);
            },
            $this->collection
        );
    }

    public function contains(\DateTimeImmutable $datetime): bool
    {
        foreach ($this as $row) {
            if ($datetime->format('Y-m-d') === $row->format('Y-m-d')) {
                return true;
            }
        }

        return false;
    }

    private function __construct()
    {

    }
}
