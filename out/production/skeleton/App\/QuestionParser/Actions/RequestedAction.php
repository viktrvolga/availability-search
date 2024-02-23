<?php

declare(strict_types=1);

namespace App\QuestionParser\Actions;

interface RequestedAction
{
    public function type(): RequestedActionType;
}
