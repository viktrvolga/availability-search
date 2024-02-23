<?php

declare(strict_types=1);

namespace App\Gpt\OpenAI\Client;

use App\Gpt\OpenAI\Client\Request\OpenAIRequest;
use App\Gpt\OpenAI\Exceptions\OpenAIInteractionFailed;
use App\Gpt\OpenAI\Exceptions\OpenAIParseResponseFailed;
use App\Gpt\OpenAI\OpenAIConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class OpenAIClient
{
    public function __construct(
        private OpenAIConfiguration $openAIConfiguration,
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private LoggerInterface     $logger
    )
    {
    }

    /**
     * @param OpenAIRequest $request
     * @return OpenAIRequestResult
     *
     * @throws OpenAIInteractionFailed
     * @throws OpenAIParseResponseFailed
     */
    public function handle(OpenAIRequest $request): OpenAIRequestResult
    {
        $sessionId = (string)Uuid::v4();

        $this->logger->debug('Request [{requestMethod}] {requestUri} to OpenAI: {requestBody}', [
            'sessionId' => $sessionId,
            'requestMethod' => $request->method(),
            'requestUri' => $request->uri(),
            'requestBody' => $request->body()
        ]);

        [$responseStatusCode, $responsePayload] = $this->execute($request);

        $this->logger->debug('OpenAI response: {responsePayload}', [
            'sessionId' => $sessionId,
            'responsePayload' => $responsePayload,
            'responseCode' => $responseStatusCode
        ]);

        return $this->parseResponse(
            statusCode: $responseStatusCode,
            content: $responsePayload,
            requestedAction: $request
        );
    }

    /**
     * @param OpenAIRequest $requestedAction
     *
     * @return array
     *
     * @throws OpenAIInteractionFailed
     */
    private function execute(OpenAIRequest $requestedAction): array
    {
        try {
            $response = $this->httpClient->request(
                method: $requestedAction->method(),
                url: $requestedAction->uri(),
                options: [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => \sprintf('Bearer %s', $this->openAIConfiguration->apiKey)
                    ],
                    'body' => $requestedAction->body()
                ]
            );

            return [$response->getStatusCode(), $response->getContent(false)];
        } catch (\Throwable $throwable) {
            throw new OpenAIInteractionFailed(
                \sprintf(
                    'Unable to execute [%s] %s request: %s',
                    $requestedAction->method(),
                    $requestedAction->uri(),
                    $throwable->getMessage()
                ),
                $throwable->getCode(),
                $throwable
            );
        }
    }

    /**
     * @param int $statusCode
     * @param string $content
     * @param OpenAIRequest $requestedAction
     *
     * @return OpenAIRequestResult
     *
     * @throws OpenAIParseResponseFailed
     */
    private function parseResponse(int $statusCode, string $content, OpenAIRequest $requestedAction): OpenAIRequestResult
    {
        try {
            if ($statusCode < 300) {
                return new OpenAIRequestSuccessResult(
                    $this->serializer->deserialize($content, $requestedAction->responseStructure(), 'json')
                );
            }

            return new OpenAIRequestFailedResult($statusCode);
        } catch (\Throwable $throwable) {
            throw new OpenAIParseResponseFailed(
                \sprintf(
                    'Unable to parse response from OpenAI ([%s] %s): %s',
                    $requestedAction->method(),
                    $requestedAction->uri(),
                    $throwable->getMessage()
                ),
                $throwable->getCode(),
                $throwable
            );
        }
    }
}
