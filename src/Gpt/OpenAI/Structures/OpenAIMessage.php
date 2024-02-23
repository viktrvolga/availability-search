<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

final readonly class OpenAIMessage
{
    public function __construct(
        public OpenAIRole $role,
        public string     $content
    )
    {
    }
}
