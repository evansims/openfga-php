<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{StoreId, StoreIdInterface};

use OpenFGA\RequestOptions\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\Requests\{CreateStoreRequest, DeleteStoreRequest, GetStoreRequest, ListStoresRequest};
use OpenFGA\Responses\{CreateStoreResponse, DeleteStoreResponse, GetStoreResponse, ListStoresResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_string;

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

        $request = (new CreateStoreRequest(
            requestFactory: $this->getRequestFactory(),
            name: $name,
            options: $options,
        ))->toRequest();

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
     * @param StoreIdInterface|string    $storeId The ID of the store to be deleted.
     * @param ?DeleteStoreRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return DeleteStoreResponse The response indicating the deletion outcome.
     */
    final public function deleteStore(
        StoreIdInterface | string $storeId,
        ?DeleteStoreRequestOptions $options = null,
    ): DeleteStoreResponse {
        $options ??= new DeleteStoreRequestOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new DeleteStoreRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            options: $options,
        ))->toRequest();

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
     * @param StoreIdInterface|string $storeId The ID of the store to be retrieved.
     * @param ?GetStoreRequestOptions $options Optional request options such as page size and continuation token.
     *
     * @return GetStoreResponse The response containing the store details.
     */
    final public function getStore(
        StoreIdInterface | string $storeId,
        ?GetStoreRequestOptions $options = null,
    ): GetStoreResponse {
        $options ??= new GetStoreRequestOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new GetStoreRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            options: $options,
        ))->toRequest();

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

        $request = (new ListStoresRequest(
            requestFactory: $this->getRequestFactory(),
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListStoresResponse::fromResponse($this->lastResponse);
    }
}
