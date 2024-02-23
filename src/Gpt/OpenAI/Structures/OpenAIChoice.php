<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

final readonly class OpenAIChoice
{
    public function __construct(
        public int           $index,
        public OpenAIMessage $message,
        public ?bool          $logprobs,
        public ?string        $finishReason
    )
    {
    }
}
