<?php

declare(strict_types=1);

namespace App\QuestionParser\OpenAI;

use App\QuestionParser\Actions\RequestedAction;
use App\QuestionParser\Exceptions\UnableToParseQuestion;
use App\QuestionParser\QuestionParser;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OpenAIQuestionParser implements QuestionParser
{
    public function __construct(
        private HttpClientInterface $openaiHttpClient,
        private SerializerInterface $serializer,
    )
    {
    }

    public function standardize(string $phrase): RequestedAction
    {
        try {
         //   $response = $this->openaiHttpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
         //       'body' => $this->serializer->serialize(OpenAIRequest::default($phrase), 'json')
         //   ]);

            //$responseCode = $response->getStatusCode();
            //$responsePayload = $response->getContent(false);

            $responseCode = 200;
            $responsePayload = <<<HTML
{
  "id": "chatcmpl-8txJKCXOXkzmpKZEggNcm4s86du1n",
  "object": "chat.completion",
  "created": 1708346722,
  "model": "gpt-3.5-turbo-0125",
  "choices": [
    {
      "index": 0,
      "message": {
        "role": "assistant",
        "content": "list|Amsterdam|unknown:criteria"
      },
      "logprobs": null,
      "finish_reason": "stop"
    }
  ],
  "usage": {
    "prompt_tokens": 139,
    "completion_tokens": 8,
    "total_tokens": 147
  },
  "system_fingerprint": "fp_6dd124df95"
}

HTML;

            if ($responseCode !== 200) {
                throw new UnableToParseQuestion(\sprintf('Error interacting with OpenAI: %d', $responseCode));
            }

            return $this->parseResponse($responsePayload);
        } catch (UnableToParseQuestion $e) {
            throw $e;
        } catch (\Throwable $throwable) {
            throw new UnableToParseQuestion(
                \sprintf('Error preparing request to OpenAI: %s', $throwable->getMessage())
            );
        }
    }

    private function parseResponse(string $jsonBody): RequestedAction
    {
        try {
            $responseModel = $this->serializer->deserialize($jsonBody, OpenAIResponse::class, 'json');
dd($responseModel);
            throw new \Exception('could not find expected fields');
        } catch (\Throwable $throwable) {
            throw new UnableToParseQuestion(
                \sprintf('Could not parse response from OpenAI: %s', $throwable->getMessage())
            );
        }
    }
}
