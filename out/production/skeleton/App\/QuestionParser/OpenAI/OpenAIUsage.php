<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OpenAIUsage
{
    #[SerializedName('prompt_tokens')]
    public int $promptSymbolsCount;
    #[SerializedName('completion_tokens')]
    public int $resultSymbolsCount;

    private function __construct()
    {
    }
}
