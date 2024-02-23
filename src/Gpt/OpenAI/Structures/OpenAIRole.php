<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

enum OpenAIRole: string
{
    /**
     * This message type is responsible for the context of the question that the user enters.
     * It is necessary for OpenAI to understand what we are talking about and not ask questions.
     */
    case SYSTEM = "system";

    /**
     * Question asked by user.
     */
    case USER = "user";

    /**
     * This type of message is used only for responses received to a question asked by the user.
     * @see https://platform.openai.com/docs/assistants/overview/agents
     */
    case ASSISTANT = "assistant";
}
