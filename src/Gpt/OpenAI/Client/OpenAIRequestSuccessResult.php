<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Client;

final readonly class OpenAIRequestSuccessResult implements OpenAIRequestResult
{
    public function __construct(
        public object $structure
    )
    {
    }
}
