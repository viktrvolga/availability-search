<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI;

use App\Gpt\Exceptions\UnableToParseQuestion;
use App\Gpt\GptDialogueProcessor;
use App\Gpt\OpenAI\Client\OpenAIClient;
use App\Gpt\OpenAI\Client\OpenAIRequestSuccessResult;
use App\Gpt\OpenAI\Client\Request\OpenAIDialogueRequest;
use App\Gpt\OpenAI\Structures\OpenAIModel;
use App\Gpt\OpenAI\Transformers\OpenAIReservationAnswerTransformer;
use App\Gpt\ReservationRequest;

final readonly class OpenAIDialogueProcessor implements GptDialogueProcessor
{
    public function __construct(
        private OpenAIClient $openAIClient,
    )
    {
    }

    /**
     * @param string $question
     *
     * @return ReservationRequest
     *
     * @throws UnableToParseQuestion
     */
    public function askAboutReservation(string $question): ReservationRequest
    {
        $response = $this->openAIClient->handle(
            OpenAIDialogueRequest::askToParseReservationRequest(
                customerMessage: $question,
                model: OpenAIModel::VERSION_3_5_TURBO
            )
        );

        if ($response instanceof OpenAIRequestSuccessResult) {
            return OpenAIReservationAnswerTransformer::transform($response->structure);
        }

        throw new UnableToParseQuestion('Failed to ask a question to AI');
    }
}
