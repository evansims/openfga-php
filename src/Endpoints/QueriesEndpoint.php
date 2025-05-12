<?php

declare(strict_types=1);

namespace OpenFGA\Endpoints;

use OpenFGA\Models\{AuthorizationModelId, StoreId};

use OpenFGA\Models\{AuthorizationModelIdInterface, ContextualTupleKeysInterface, StoreIdInterface, TupleKeyInterface, UserTypeFilters};
use OpenFGA\RequestOptions\{CheckOptions, ExpandOptions, ListObjectsOptions, ListUsersOptions};
use OpenFGA\Requests\{CheckRequest, ExpandRequest, ListObjectsRequest, ListUsersRequest};
use OpenFGA\Responses\{CheckResponse, ExpandResponse, ListObjectsResponse, ListUsersResponse};
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_string;

/**
 * Trait containing methods for querying relationships in OpenFGA.
 */
trait QueriesEndpoint
{
    public ?RequestInterface $lastRequest = null;

    public ?ResponseInterface $lastResponse = null;

    final public function check(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?CheckOptions $options = null,
    ): CheckResponse {
        $options ??= new CheckOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new CheckRequest(
            requestFactory: $this->getRequestFactory(),
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            tupleKey: $tupleKey,
            trace: $trace,
            context: $context,
            contextualTuples: $contextualTuples,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return CheckResponse::fromResponse($this->lastResponse);
    }

    final public function expand(
        StoreIdInterface | string $storeId,
        TupleKeyInterface $tupleKey,
        AuthorizationModelIdInterface | string | null $authorizationModelId = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ExpandOptions $options = null,
    ): ExpandResponse {
        $options ??= new ExpandOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        if (null !== $authorizationModelId) {
            $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;
        }

        $request = (new ExpandRequest(
            requestFactory: $this->getRequestFactory(),
            tupleKey: $tupleKey,
            contextualTuples: $contextualTuples,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ExpandResponse::fromResponse($this->lastResponse);
    }

    final public function listObjects(
        StoreIdInterface | string $storeId,
        string $type,
        string $relation,
        string $user,
        AuthorizationModelIdInterface | string | null $authorizationModelId = null,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ListObjectsOptions $options = null,
    ): ListObjectsResponse {
        $options ??= new ListObjectsOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;

        if (null !== $authorizationModelId) {
            $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;
        }

        $request = (new ListObjectsRequest(
            requestFactory: $this->getRequestFactory(),
            type: $type,
            relation: $relation,
            user: $user,
            context: $context,
            contextualTuples: $contextualTuples,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListObjectsResponse::fromResponse($this->lastResponse);
    }

    final public function listUsers(
        StoreIdInterface | string $storeId,
        AuthorizationModelIdInterface | string $authorizationModelId,
        string $object,
        string $relation,
        UserTypeFilters $userFilters,
        ?object $context = null,
        ?ContextualTupleKeysInterface $contextualTuples = null,
        ?ListUsersOptions $options = null,
    ): ListUsersResponse {
        $options ??= new ListUsersOptions();
        $storeId = is_string($storeId) ? StoreId::fromString($storeId) : $storeId;
        $authorizationModelId = is_string($authorizationModelId) ? AuthorizationModelId::fromString($authorizationModelId) : $authorizationModelId;

        $request = (new ListUsersRequest(
            requestFactory: $this->getRequestFactory(),
            object: $object,
            relation: $relation,
            userFilters: $userFilters,
            context: $context,
            contextualTuples: $contextualTuples,
            storeId: $storeId,
            authorizationModelId: $authorizationModelId,
            options: $options,
        ))->toRequest();

        $this->lastRequest = $request->getRequest();
        $this->lastResponse = $request->send();

        return ListUsersResponse::fromResponse($this->lastResponse);
    }
}
