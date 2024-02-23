<?php

declare(strict_types=1);

namespace App\Common\Structures;

final readonly class DateTimeRange
{
    public function __construct(
        public \DateTimeImmutable $from,
        public \DateTimeImmutable $to
    )
    {
    }

    public function isSame(): bool
    {
        return $this->from->format('Y-m-d') !== $this->to->format('Y-m-d');
    }
}
