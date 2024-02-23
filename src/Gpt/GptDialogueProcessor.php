<?php

declare(strict_types=1);

namespace App\Gpt;

use App\Gpt\Exceptions\UnableToParseQuestion;

interface GptDialogueProcessor
{
    /**
     * @todo: more general solution. Support another type of questions.
     *
     * @param string $question
     * @return ReservationRequest
     *
     * @throws UnableToParseQuestion
     */
    public function askAboutReservation(string $question): ReservationRequest;
}
