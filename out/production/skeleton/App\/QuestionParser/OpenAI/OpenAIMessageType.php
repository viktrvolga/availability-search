<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

enum OpenAIMessageType: string
{
    case REQUIREMENTS = "system";
    case QUESTION = "user";
    case ASSISTANT = "assistant";
}
