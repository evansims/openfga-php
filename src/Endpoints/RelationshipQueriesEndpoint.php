<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{AuthorizationModelIdInterface, ConsistencyPreference, ContextualTupleKeysInterface, StoreIdInterface, TupleKeyInterface, UserTypeFilters};
use OpenFGA\RequestOptions\{CheckOptions, ExpandOptions, ListObjectsOptions, ListUsersOptions};
use OpenFGA\Requests\{CheckRequest, ExpandRequest, ListObjectsRequest, ListUsersRequest};
use OpenFGA\Responses\{CheckResponse, ExpandResponse, ListObjectsResponse, ListUsersResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

/**
 * Trait containing methods for querying relationships in OpenFGA.
 */
trait RelationshipQueriesEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    final public function check(
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ConsistencyPreference $consistency = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?CheckOptions $options = null,
    ): CheckResponse {
        $options ??= new CheckOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = (new CheckRequest(
            requestFactory: $this->getRequestFactory(),
            tupleKey: $tupleKey,
            trace: $trace,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return CheckResponse::fromResponse($this->lastResponse);
    }

    final public function expand(
        TupleKeyInterface $tupleKey,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ConsistencyPreference $consistency = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?ExpandOptions $options = null,
    ): ExpandResponse {
        $options ??= new ExpandOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = (new ExpandRequest(
            requestFactory: $this->getRequestFactory(),
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ExpandResponse::fromResponse($this->lastResponse);
    }

    final public function listObjects(
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ConsistencyPreference $consistency = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?ListObjectsOptions $options = null,
    ): ListObjectsResponse {
        $options ??= new ListObjectsOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = (new ListObjectsRequest(
            requestFactory: $this->getRequestFactory(),
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListObjectsResponse::fromResponse($this->lastResponse);
    }

    final public function listUsers(
        string $object,
        string $relation,
        UserTypeFilters $userFilters,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ConsistencyPreference $consistency = null,
        ?StoreIdInterface $storeId = null,
        ?AuthorizationModelIdInterface $authorizationModelId = null,
        ?ListUsersOptions $options = null,
    ): ListUsersResponse {
        $options ??= new ListUsersOptions();
        $storeId = $this->getStoreId($storeId);
        $authorizationModelId = $this->getAuthorizationModelId($authorizationModelId);

        $request = (new ListUsersRequest(
            requestFactory: $this->getRequestFactory(),
            object: $object,
            relation: $relation,
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: $consistency,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListUsersResponse::fromResponse($this->lastResponse);
    }
}
