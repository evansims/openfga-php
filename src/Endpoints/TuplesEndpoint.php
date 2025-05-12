<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{AuthorizationModelId, AuthorizationModelIdInterface, StoreId, StoreIdInterface, TupleKeyInterface, TupleKeysInterface};
use OpenFGA\RequestOptions\{ListChangesOptions, ListTuplesOptions, WriteTuplesOptions};
use OpenFGA\Requests\{ListChangesRequest, ListTuplesRequest, WriteTuplesRequest};
use OpenFGA\Responses\{ListChangesResponse, ListTuplesResponse, WriteTuplesResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_string;

trait TuplesEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    /**
     * Lists changes to relationship tuples for a given store.
     *
     * This function sends a GET request to the /stores/{storeId}/changes endpoint to list
     * changes to relationship tuples for a given store. It returns a ListChangesResponse object
     * containing the list of changes.
     *
     * @param StoreIdInterface|string $storeId The store ID to list changes for.
     * @param null|ListChangesOptions $options The options for the list changes request. If null, the default options are used.
     *
     * @return ListChangesResponse The response to the list changes request.
     */
    final public function listChanges(
        StoreIdInterface | string $storeId,
        ?ListChangesOptions $options = null,
    ): ListChangesResponse {
        $options ??= new ListChangesOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new ListChangesRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListChangesResponse::fromResponse($this->lastResponse);
    }

    final public function listTuples(
        StoreIdInterface | string $storeId,
        ?TupleKeyInterface $tupleKey = null,
        ?ListTuplesOptions $options = null,
    ): ListTuplesResponse {
        $options ??= new ListTuplesOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        $request = (new ListTuplesRequest(
            requestFactory: $this->getRequestFactory(),
            tupleKey: $tupleKey,
            storeId: $storeId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListTuplesResponse::fromResponse($this->lastResponse);
    }

    final public function writeTuples(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        ?WriteTuplesOptions $options = null,
    ): WriteTuplesResponse {
        $options ??= new WriteTuplesOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new WriteTuplesRequest(
            requestFactory: $this->getRequestFactory(),
            writes: $writes,
            deletes: $deletes,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return WriteTuplesResponse::fromResponse($this->lastResponse);
    }
}
