<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

enum OpenAIModel: string
{
    case LATEST = "gpt-4-0125-preview";

    case VERSION_4 = "gpt-4";
    case VERSION_4_TURBO = "gpt-4-turbo-preview";
    case VERSION_3_5_TURBO = 'gpt-3.5-turbo';
}
