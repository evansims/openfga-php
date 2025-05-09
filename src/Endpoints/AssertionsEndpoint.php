<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{Assertions};
use OpenFGA\RequestOptions\{ReadAssertionsOptions, WriteAssertionsOptions};
use OpenFGA\Responses\{ReadAssertionsResponse, WriteAssertionsResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait AssertionsEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    /**
     * Retrieves assertions for the specified authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to retrieve assertions for a given authorization model ID. It returns a ReadAssertionsResponse object.
     *
     * @param null|string                $authorizationModelId The authorization model ID. Uses the default authorization model ID if null.
     * @param null|string                $storeId              The store ID. Uses the default store ID if null.
     * @param null|ReadAssertionsOptions $options              Optional request options such as page size and continuation token.
     *
     * @return ReadAssertionsResponse The response containing the assertions.
     */
    final public function readAssertions(
        ?string $authorizationModelId = null,
        ?string $storeId = null,
        ?ReadAssertionsOptions $options = null,
    ): ReadAssertionsResponse {
        $options ??= new ReadAssertionsOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/assertions/' . $authorizationModelId),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ReadAssertionsResponse::fromResponse($this->lastResponse);
    }

    /**
     * Upserts assertions for the specified authorization model.
     *
     * This function sends a PUT request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to upsert assertions for a given authorization model ID. It returns a WriteAssertionsResponse object.
     *
     * @param Assertions                  $assertions           The assertions to write.
     * @param null|string                 $authorizationModelId The authorization model ID. Uses the default authorization model ID if null.
     * @param null|string                 $storeId              The store ID. Uses the default store ID if null.
     * @param null|WriteAssertionsOptions $options              Optional request options.
     *
     * @return WriteAssertionsResponse The response indicating the write outcome.
     */
    final public function writeAssertions(
        Assertions $assertions,
        ?string $authorizationModelId = null,
        ?string $storeId = null,
        ?WriteAssertionsOptions $options = null,
    ): WriteAssertionsResponse {
        $options ??= new WriteAssertionsOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $jsonBody = json_encode([
            'assertions' => $assertions->toArray(),
        ]);

        // Ensure we have a valid JSON string
        if (false === $jsonBody) {
            $jsonBody = '{"assertions": []}';
        }

        $body = $this->getRequestFactory()->getHttpStreamFactory()->createStream($jsonBody);

        $request = $this->getRequestFactory()->put(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId . '/assertions/' . $authorizationModelId),
            options: $options,
            body: $body,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return WriteAssertionsResponse::fromResponse($this->lastResponse);
    }
}
