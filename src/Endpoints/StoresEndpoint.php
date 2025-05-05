<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\RequestOptions\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\Responses\{CreateStoreResponse, DeleteStoreResponse, GetStoreResponse, ListStoresResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait StoresEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    /**
     * Creates a new store with the given name.
     *
     * This function sends a POST request to the /stores endpoint to create
     * a new store. It returns a CreateStoreResponse object containing the
     * details of the created store.
     *
     * @param string                     $name    The name of the store to be created.
     * @param ?CreateStoreRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return CreateStoreResponse The response containing the details of the created store.
     */
    final public function createStore(
        string $name,
        ?CreateStoreRequestOptions $options = null,
    ): CreateStoreResponse {
        $options ??= new CreateStoreRequestOptions();
        $name = trim($name);

        $body = $this->getRequestFactory()->getHttpStreamFactory()->createStream(json_encode([
            'name' => $name,
        ]));

        $request = $this->getRequestFactory()->post(
            url: $this->getRequestFactory()->getEndpointUrl('/stores'),
            options: $options,
            body: $body,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return CreateStoreResponse::fromResponse($this->lastResponse);
    }

    /**
     * Deletes a store with the specified ID.
     *
     * This function sends a DELETE request to the /stores/{store_id} endpoint
     * to delete a store. It returns a DeleteStoreResponse object.
     *
     * @param ?string                    $storeId The ID of the store to be deleted. Uses the default store ID if null.
     * @param ?DeleteStoreRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return DeleteStoreResponse The response indicating the deletion outcome.
     */
    final public function deleteStore(
        ?string $storeId = null,
        ?DeleteStoreRequestOptions $options = null,
    ): DeleteStoreResponse {
        $options ??= new DeleteStoreRequestOptions();
        $storeId = $this->getStoreId($storeId);

        $request = $this->getRequestFactory()->delete(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return DeleteStoreResponse::fromResponse($this->lastResponse);
    }

    /**
     * Retrieves a store by its ID.
     *
     * Sends a GET request to the /stores/{store_id} endpoint and returns a GetStoreResponse
     * object that contains the store details.
     *
     * @param ?string                 $storeId The ID of the store to be retrieved. Uses the default store ID if null.
     * @param ?GetStoreRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return GetStoreResponse The response containing the store details.
     */
    final public function getStore(
        ?string $storeId = null,
        ?GetStoreRequestOptions $options = null,
    ): GetStoreResponse {
        $options ??= new GetStoreRequestOptions();
        $storeId = $this->getStoreId($storeId);

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores/' . $storeId),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return GetStoreResponse::fromResponse($this->lastResponse);
    }

    /**
     * Retrieves a list of all stores.
     *
     * Sends a GET request to the /stores endpoint and returns a ListStoresResponse
     * object that contains the list of stores and a continuation token for pagination.
     *
     * @param ?ListStoresRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return ListStoresResponse The response containing the list of stores and pagination details.
     */
    final public function listStores(
        ?ListStoresRequestOptions $options = null,
    ): ListStoresResponse {
        $options ??= new ListStoresRequestOptions();

        $request = $this->getRequestFactory()->get(
            url: $this->getRequestFactory()->getEndpointUrl('/stores'),
            options: $options,
            headers: $this->getRequestFactory()->getEndpointHeaders(),
        );

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListStoresResponse::fromResponse($this->lastResponse);
    }
}
