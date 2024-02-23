<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OpenAIRequest
{
    private const MAX_RESPONSE_TOKENS_COUNT = 20;
    private const MAX_CHAT_COMPLETIONS_COUNT = 1;
    private const REQUIREMENTS_TEMPLATE = <<<TEXT
parse the question entered by the user.
example:I want to book a table in a restaurant in Hoorn today/Show me a list of available restaurants in Hoorn for the tomorrow evening.
All answers must be in English.format "intent|city|ISO date|time of day (or any)".
intent values:book,list,availability.
convert date to datetime.If a range,then format is from:to.
If you dont understand the question,return "unknown:intent".
If there is no iso date/city,return "unknown:criteria".
TEXT;

    /**
     * @param OpenAIModel $model
     * @param array<OpenAIMessage> $messages
     * @param int $maxResponseTokensCount
     * @param int $maxChatCompletionChoicesCount
     */
    public function __construct(
        public OpenAIModel $model,
        public array       $messages,
        #[SerializedName('max_tokens')]
        public int         $maxResponseTokensCount = self::MAX_RESPONSE_TOKENS_COUNT,
        #[SerializedName('n')]
        public int         $maxChatCompletionChoicesCount = self::MAX_CHAT_COMPLETIONS_COUNT,

    )
    {
    }

    public static function default(string $phrase, OpenAIModel $model = OpenAIModel::VERSION_3_5_TURBO): OpenAIRequest
    {
        return new self(
            model: $model,
            messages: [
                OpenAIMessage::create(
                    role: OpenAIMessageType::REQUIREMENTS,
                    content: self::REQUIREMENTS_TEMPLATE
                ),
                OpenAIMessage::create(
                    role: OpenAIMessageType::QUESTION,
                    content: $phrase

                )
            ]
        );
    }
}
