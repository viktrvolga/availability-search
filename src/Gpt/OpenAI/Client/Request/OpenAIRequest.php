<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Client\Request;

interface OpenAIRequest
{
    public function uri(): string;

    public function method(): string;

    public function body(): ?string;

    public function responseStructure(): string;
}
