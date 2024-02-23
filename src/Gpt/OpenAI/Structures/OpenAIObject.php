<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

enum OpenAIObject: string
{
    case CHAT_COMPLETION = "chat.completion";
}
