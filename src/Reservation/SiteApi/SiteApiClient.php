<?php

declare(strict_types=1);

namespace App\Reservation\SiteApi;

use App\Reservation\Exceptions\UnableToFetchProductDetails;
use App\Reservation\SiteApi\Requests\SiteApiRequest;
use Symfony\Component\HttpClient\AmpHttpClient;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class SiteApiClient
{
    public function __construct(
        private AmpHttpClient       $httpClient,
        private SerializerInterface $serializer
    )
    {
    }

    public function handle(SiteApiRequest $action): object
    {
        $response = $this->httpClient->request(
            $action->method(),
            $action->url(),
            $action->parameters()
        );

        $responsePayload = $response->getContent(false);
        $responseCode = $response->getStatusCode();

        if ($responseCode !== 200) {
            throw new UnableToFetchProductDetails(
                \sprintf('Error during execution of %s action: %d (%s)',
                    \get_class($action),
                    $responseCode,
                    $action->url()
                )
            );
        }

        /** @todo: use normalizer directly */
        if (\is_a($action->responseStructure(), \IteratorAggregate::class, true)) {
            $responseData = \json_decode($responsePayload, true, 512, \JSON_THROW_ON_ERROR);
            $responsePayload = \json_encode(['collection' => $responseData]);
        }

        return $this->serializer->deserialize(
            $responsePayload,
            $action->responseStructure(),
            'json'
        );
    }
}
