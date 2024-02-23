<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi\Requests;

interface SiteApiRequest
{
    public function url(): string;

    public function method(): string;

    public function parameters(): array;
    
    public function responseStructure(): string;
}
