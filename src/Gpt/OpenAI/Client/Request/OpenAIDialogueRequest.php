<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Client\Request;

use App\Gpt\OpenAI\Structures\OpenAIChatCompletion;
use App\Gpt\OpenAI\Structures\OpenAIMessage;
use App\Gpt\OpenAI\Structures\OpenAIModel;
use App\Gpt\OpenAI\Structures\OpenAIRole;

/**
 * @see https://platform.openai.com/docs/api-reference/chat/create
 */
final readonly class OpenAIDialogueRequest implements OpenAIRequest
{
    private const DEFAULT_RESPONSE_TOKENS_COUNT = 40;
    private const DEFAULT_CHAT_COMPLETIONS_COUNT = 1;

    private const RESERVATION_CONTEXT_TEMPLATE = <<<TEXT
parse a request from a user.
also find me the coordinates of the city specified by the user.
example:I want to book a table in a restaurant in Hoorn today/Show me a list of restaurants in Hoorn for the tomorrow evening/I want to book a best restaurant on Monday in Amsterdam.
All answers must be in English.format "reservation|persons_count (or 1)|city name|city latitude|city longitude|ISO date|time of day (or any)".
convert date to datetime.If a range,then format is "from_date to_date".
If the day of the week is specified, then this must be taken into account.
If you dont understand the question,return "reservation|unknown".
If you dont understand the date/city,return "reservation|unknown:criteria".
TEXT;

    /**
     * @param OpenAIModel $model
     * @param OpenAIMessage[] $messageCollection
     * @param int $maxResponseSymbolsCount
     * @param int $maxResponseChatCompletionsCount
     */
    public function __construct(
        private OpenAIModel $model,
        private array       $messageCollection,
        private int         $maxResponseSymbolsCount = self::DEFAULT_RESPONSE_TOKENS_COUNT,
        private int         $maxResponseChatCompletionsCount = self::DEFAULT_CHAT_COMPLETIONS_COUNT
    )
    {
    }

    public static function askToParseReservationRequest(
        string      $customerMessage,
        OpenAIModel $model
    ): self
    {
        return new self(
            model: $model,
            messageCollection: [
                new OpenAIMessage(
                    role: OpenAIRole::SYSTEM,
                    content: \sprintf(
                        '%s. Current date: %s',
                        self::RESERVATION_CONTEXT_TEMPLATE,
                        date('Y-m-d H:i:s')
                    )
                ),
                new OpenAIMessage(
                    role: OpenAIRole::USER,
                    content: $customerMessage
                )
            ]
        );
    }

    public function uri(): string
    {
        return 'https://api.openai.com/v1/chat/completions';
    }

    public function method(): string
    {
        return 'POST';
    }

    public function body(): ?string
    {
        return \json_encode(
            [
                'model' => $this->model->value,
                'max_tokens' => $this->maxResponseSymbolsCount,
                'n' => $this->maxResponseChatCompletionsCount,
                'messages' => \array_map(
                    static function (OpenAIMessage $message): array {
                        return [
                            'role' => $message->role->value,
                            'content' => self::sanitize($message->content)
                        ];
                    },
                    $this->messageCollection
                )
            ],
            \JSON_UNESCAPED_UNICODE
        );
    }

    public function responseStructure(): string
    {
        return OpenAIChatCompletion::class;
    }

    private static function sanitize(string $content): string
    {
        return \trim(\str_replace(array("\r\n", "\r", "\n"), '', \strip_tags($content)));
    }
}
