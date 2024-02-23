<?php

declare(strict_types=1);

namespace App\QuestionParser;

use App\QuestionParser\Actions\RequestedAction;

interface QuestionParser
{
    public function standardize(string $phrase): RequestedAction;
}
