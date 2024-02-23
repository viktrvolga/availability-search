<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Structures;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OpenAIChatCompletion
{
    /**
     * @param string $id
     * @param OpenAIObject $object
     * @param int $created
     * @param OpenAIModel $model
     * @param string $systemFingerprint
     * @param OpenAIChoice[] $choices
     */
    public function __construct(
        public string       $id,
        public OpenAIObject $object,
        public int          $created,
        public OpenAIModel  $model,
        #[SerializedName('system_fingerprint')]
        public string       $systemFingerprint,
        public array        $choices
    )
    {
    }
}
