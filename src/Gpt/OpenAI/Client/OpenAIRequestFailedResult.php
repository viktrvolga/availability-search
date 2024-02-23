<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Client;

final readonly class OpenAIRequestFailedResult implements OpenAIRequestResult
{
    public function __construct(
        public int $statusCode
    )
    {
    }
}
