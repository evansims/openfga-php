<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Messages;
use OpenFGA\Models\{AuthorizationModelInterface, StoreInterface, TupleKeyInterface};
use OpenFGA\Models\Collections\{BatchCheckItemsInterface, TupleKeysInterface, UserTypeFiltersInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Requests\{BatchCheckRequest, CheckRequest, ExpandRequest, ListObjectsRequest, ListUsersRequest, StreamedListObjectsRequest};
use OpenFGA\Responses\{BatchCheckResponse, CheckResponse, ExpandResponse, ListObjectsResponse, ListUsersResponse, StreamedListObjectsResponse};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\RequestInterface as HttpRequestInterface;
use Throwable;

/**
 * Service implementation for authorization operations.
 *
 * This service handles all authorization-related queries including permission checks,
 * relationship expansions, and object/user listing. It delegates HTTP communication
 * to the HttpServiceInterface and uses the Result pattern for consistent error handling.
 *
 * The service supports various consistency levels and contextual tuple evaluation
 * for dynamic authorization scenarios. All operations are performed against a specific
 * store and authorization model.
 *
 * @see AuthorizationServiceInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries Authorization query operations
 */
final readonly class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Schema validator for response validation.
     */
    private SchemaValidator $validator;

    /**
     * Create a new authorization service instance.
     *
     * @param HttpServiceInterface $httpService The HTTP service for making API requests
     */
    public function __construct(
        private HttpServiceInterface $httpService,
    ) {
        $this->validator = new SchemaValidator;
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function batchCheck(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        BatchCheckItemsInterface $checks,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new BatchCheckRequest(
                store: $this->getStoreId($store),
                model: $this->getModelId($model),
                checks: $checks,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(BatchCheckResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function check(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        TupleKeyInterface $tupleKey,
        ?bool $trace = null,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new CheckRequest(
                store: $this->getStoreId($store),
                model: $this->getModelId($model),
                tupleKey: $tupleKey,
                trace: $trace,
                context: $context,
                contextualTuples: $contextualTuples,
                consistency: $consistency,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(CheckResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function expand(
        StoreInterface | string $store,
        TupleKeyInterface $tupleKey,
        AuthorizationModelInterface | string | null $model = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new ExpandRequest(
                tupleKey: $tupleKey,
                contextualTuples: $contextualTuples,
                store: $this->getStoreId($store),
                model: (null !== $model) ? $this->getModelId($model) : null,
                consistency: $consistency,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(ExpandResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function listObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new ListObjectsRequest(
                type: $type,
                relation: $relation,
                user: $user,
                context: $context,
                contextualTuples: $contextualTuples,
                store: $this->getStoreId($store),
                model: $this->getModelId($model),
                consistency: $consistency,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(ListObjectsResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function listUsers(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $object,
        string $relation,
        UserTypeFiltersInterface $userFilters,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new ListUsersRequest(
                object: $object,
                relation: $relation,
                userFilters: $userFilters,
                context: $context,
                contextualTuples: $contextualTuples,
                store: $this->getStoreId($store),
                model: $this->getModelId($model),
                consistency: $consistency,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(ListUsersResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws Throwable If the request fails
     */
    #[Override]
    public function streamedListObjects(
        StoreInterface | string $store,
        AuthorizationModelInterface | string $model,
        string $type,
        string $relation,
        string $user,
        ?object $context = null,
        ?TupleKeysInterface $contextualTuples = null,
        ?Consistency $consistency = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new StreamedListObjectsRequest(
                type: $type,
                relation: $relation,
                user: $user,
                context: $context,
                contextualTuples: $contextualTuples,
                store: $this->getStoreId($store),
                model: $this->getModelId($model),
                consistency: $consistency,
            );

            $response = $this->httpService->send($request);
            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof HttpRequestInterface) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::NO_LAST_REQUEST_FOUND), ]);
            }

            return new Success(StreamedListObjectsResponse::fromResponse($response, $lastRequest, $this->getValidator()));
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * Get the authorization model ID from a given authorization model.
     *
     * If an instance of AuthorizationModelInterface is provided, the ID will be
     * retrieved from the object using the getId() method. Otherwise, the value
     * will be used as the authorization model ID.
     *
     * @param  AuthorizationModelInterface|string $model The authorization model to get the ID from
     * @return string                             The authorization model ID
     */
    private function getModelId(AuthorizationModelInterface | string $model): string
    {
        if ($model instanceof AuthorizationModelInterface) {
            return $model->getId();
        }

        return $model;
    }

    /**
     * Get the store ID from the given store.
     *
     * If the given store is an instance of StoreInterface, the ID will be retrieved
     * from the object using the getId() method. Otherwise, the given value will be
     * used as the store ID.
     *
     * @param  StoreInterface|string $store The store to get the ID from
     * @return string                The store ID
     */
    private function getStoreId(StoreInterface | string $store): string
    {
        if ($store instanceof StoreInterface) {
            return $store->getId();
        }

        return $store;
    }

    /**
     * Get the schema validator instance.
     *
     * @return SchemaValidator The validator instance
     */
    private function getValidator(): SchemaValidator
    {
        return $this->validator;
    }
}
