<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use OpenFGA\RequestOptions\{ReadAssertionsOptions, WriteAssertionsOptions};
use OpenFGA\Responses\{ReadAssertionsResponse, WriteAssertionsResponse};
use OpenFGA\Models\{Assertions};

trait AssertionsEndpoint
{
    public ?RequestInterface $lastRequest = null;
    public ?ResponseInterface $lastResponse = null;

    /**
     * Read assertions for the specified authorization model.
     *
     * This function sends a GET request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to retrieve assertions for a given authorization model ID. It returns a ReadAssertionsResponse object.
     *
     * @param string|null $authorizationModelId The authorization model ID. Uses the default authorization model ID if null.
     * @param string|null $storeId The store ID. Uses the default store ID if null.
     * @param ReadAssertionsOptions|null $options Optional request options such as page size and continuation token.
     *
     * @return ReadAssertionsResponse The response containing the assertions.
     */
    final public function readAssertions(
        ?string $authorizationModelId = null,
        ?string $storeId = null,
        ?ReadAssertionsOptions $options = null,
    ): ReadAssertionsResponse
    {
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
     * Write assertions for the specified authorization model.
     *
     * This function sends a PUT request to the /stores/{store_id}/assertions/{authorization_model_id} endpoint
     * to upsert assertions for a given authorization model ID. It returns a WriteAssertionsResponse object.
     *
     * @param Assertions $assertions The assertions to write.
     * @param string|null $authorizationModelId The authorization model ID. Uses the default authorization model ID if null.
     * @param string|null $storeId The store ID. Uses the default store ID if null.
     * @param WriteAssertionsOptions|null $options Optional request options.
     *
     * @return WriteAssertionsResponse The response indicating the write outcome.
     */
    final public function writeAssertions(
        Assertions $assertions,
        ?string $authorizationModelId = null,
        ?string $storeId = null,
        ?WriteAssertionsOptions $options = null,
    ): WriteAssertionsResponse
    {
        $options ??= new WriteAssertionsOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $body = $this->getRequestFactory()->getHttpStreamFactory()->createStream(json_encode([
            'assertions' => $assertions->toArray(),
        ]));

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
