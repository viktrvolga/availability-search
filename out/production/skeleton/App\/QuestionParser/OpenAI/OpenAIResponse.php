<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OpenAIResponse
{
    #[SerializedName('id')]
    public string $id;

    private function __construct()
    {
    }
}
