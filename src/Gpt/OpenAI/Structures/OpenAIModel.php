<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

enum OpenAIModel: string
{
    /**
     * One of the cheapest versions, which is enough to solve our problem.
     *
     * @see https://platform.openai.com/docs/models/gpt-3-5-turbo
     * @see https://openai.com/pricing
     */
    case VERSION_3_5_TURBO = 'gpt-3.5-turbo-0125';
}
