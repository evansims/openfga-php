<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use Exception;
use OpenFGA\RequestOptions\{ListChangesOptions, ListTuplesOptions};
use DateTimeImmutable;
use OpenFGA\Models\{ConsistencyPreference, StoreIdInterface, AuthorizationModelIdInterface, TupleKeyInterface};
use OpenFGA\Responses\ListChangesResponse;
use OpenFGA\Requests\ListChangesRequest;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

trait RelationshipTuplesEndpoint
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
     * @param string|null $type The type of tuples to list changes for. If null, all types are included.
     * @param DateTimeImmutable|null $startTime The start time for the list of changes. If null, all changes are included.
     * @param StoreIdInterface|null $storeId The store ID to list changes for. If null, the default store ID is used.
     * @param AuthorizationModelIdInterface|null $authorizationModelId The authorization model ID to list changes for. If null, the default authorization model ID is used.
     * @param ListChangesOptions|null $options The options for the list changes request. If null, the default options are used.
     *
     * @return ListChangesResponse The response to the list changes request.
     */
    final public function listChanges(
        ?string $type = null,
        ?DateTimeImmutable $startTime = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?ListChangesOptions $options = null,
    ): ListChangesResponse {
        $options ??= new ListChangesOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = (new ListChangesRequest(
            requestFactory: $this->getRequestFactory(),
            type: $type,
            startTime: $startTime,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListChangesResponse::fromResponse($this->lastResponse);
    }

    final public function listTuples(
        ?TupleKeyInterface $tupleKey = null,
        ?ConsistencyPreference $consistency = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?ListTuplesOptions $options = null,
    ): ListTuplesResponse {
        $options ??= new ListTuplesOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/read',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/read failed");
        }

        $json = $api->getResponseBodyJson();

        return new ReadResponse($json);
    }

    final public function writeTuples(
        ?TupleKeysInterface $writes = null,
        ?TupleKeysInterface $deletes = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?WriteOptions $options = null,
    ): void {
        $options ??= new WriteOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        if (null === $options) {
            $options = new WriteOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/write',
        );

        $this->lastRequest = $api->getRequest();

        $response = $api->post();

        $this->lastResponse = $response;

        if (200 !== $response->getStatusCode()) {
            throw new Exception("POST /stores/{$storeId}/write failed");
        }

    }
}
