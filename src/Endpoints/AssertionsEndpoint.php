<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{Assertions, AuthorizationModelId, AuthorizationModelIdInterface, StoreId, StoreIdInterface};

use OpenFGA\RequestOptions\{ReadAssertionsOptions, WriteAssertionsOptions};
use OpenFGA\Requests\{ReadAssertionsRequest, WriteAssertionsRequest};
use OpenFGA\Responses\{ReadAssertionsResponse, WriteAssertionsResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_string;

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
     * @param StoreIdInterface|string              $storeId              The store ID.
     * @param AuthorizationModelIdInterface|string $authorizationModelId The authorization model ID.
     * @param null|ReadAssertionsOptions           $options              Optional request options such as page size and continuation token.
     *
     * @return ReadAssertionsResponse The response containing the assertions.
     */
    final public function readAssertions(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        ?ReadAssertionsOptions $options = null,
    ): ReadAssertionsResponse {
        $options ??= new ReadAssertionsOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new ReadAssertionsRequest(
            requestFactory: $this->getRequestFactory(),
            authorizationModelId: $authorizationModelId,
            storeId: $storeId,
            options: $options,
        ))->toRequest();

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
     * @param StoreIdInterface|string              $storeId              The store ID.
     * @param AuthorizationModelIdInterface|string $authorizationModelId The authorization model ID.
     * @param Assertions                           $assertions           The assertions to write.
     * @param null|WriteAssertionsOptions          $options              Optional request options.
     *
     * @return WriteAssertionsResponse The response indicating the write outcome.
     */
    final public function writeAssertions(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        Assertions $assertions,
        ?WriteAssertionsOptions $options = null,
    ): WriteAssertionsResponse {
        $options ??= new WriteAssertionsOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new WriteAssertionsRequest(
            requestFactory: $this->getRequestFactory(),
            assertions: $assertions,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return WriteAssertionsResponse::fromResponse($this->lastResponse);
    }
}
