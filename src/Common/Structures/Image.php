<?php

declare(strict_types=1);

namespace App\Common\Structures;

final readonly class Image
{
    public function __construct(
        public int     $id,
        public ?string $altText,
        public string  $path,
        public ?string $type
    )
    {
    }
}
