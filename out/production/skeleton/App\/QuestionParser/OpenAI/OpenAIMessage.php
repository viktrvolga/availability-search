<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

final readonly class OpenAIMessage
{
    public OpenAIMessageType $role;
    public string $content;

    public function __construct(
        OpenAIMessageType $role,
        string            $content
    )
    {
        
    }
}
