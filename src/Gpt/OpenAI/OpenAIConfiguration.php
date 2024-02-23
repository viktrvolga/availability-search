<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI;

final readonly class OpenAIConfiguration
{
    public function __construct(
        public string $apiKey
    )
    {
    }
}
