<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{Stores};
use OpenFGA\Models\{Store};
use OpenFGA\Requests\{CreateStoreRequest, DeleteStoreRequest, GetStoreRequest, ListStoresRequest};
use OpenFGA\Responses\{CreateStoreResponse, DeleteStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\Results\{Failure, ResultInterface, Success};
use OpenFGA\Schemas\SchemaValidatorInterface;
use OpenFGA\Services\HttpServiceInterface;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\RequestInterface;
use Throwable;

use function max;
use function min;
use function trim;

/**
 * HTTP implementation of the store repository.
 *
 * This repository provides a domain-focused abstraction for store operations,
 * handling all HTTP communication through the injected HttpService. It converts
 * domain objects to API requests, sends them via HTTP, and transforms responses
 * back to domain objects while maintaining proper error handling.
 *
 * The repository encapsulates all HTTP-specific concerns including request/response
 * transformation, pagination handling, and API error mapping. It follows the SDK's
 * Result pattern to provide safe error handling without exceptions for control flow.
 *
 * ## Implementation Details
 *
 * - Uses HttpService for all HTTP operations
 * - Validates responses using SchemaValidator
 * - Transforms API responses to domain objects
 * - Handles pagination for list operations
 * - Provides consistent error handling via Result pattern
 *
 * @see StoreRepositoryInterface For the domain contract
 * @see https://openfga.dev/docs/api#/Stores Store management documentation
 */
final readonly class HttpStoreRepository implements StoreRepositoryInterface
{
    private const int MAX_PAGE_SIZE = 100;

    /**
     * @param HttpServiceInterface     $httpService Service for making HTTP requests
     * @param SchemaValidatorInterface $validator   Validator for API responses
     */
    public function __construct(
        private HttpServiceInterface $httpService,
        private SchemaValidatorInterface $validator,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function create(string $name): ResultInterface
    {
        try {
            $name = trim($name);

            if ('' === $name) {
                throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_NAME_EMPTY)]);
            }

            $request = new CreateStoreRequest(name: $name);

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $createResponse = CreateStoreResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Convert response to domain Store object
            $store = new Store(
                id: $createResponse->getId(),
                name: $createResponse->getName(),
                createdAt: $createResponse->getCreatedAt(),
                updatedAt: $createResponse->getUpdatedAt(),
            );

            return new Success($store);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function delete(string $storeId): ResultInterface
    {
        try {
            $request = new DeleteStoreRequest(store: $storeId);

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            DeleteStoreResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return success with no data for delete operations
            return new Success(null);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function get(string $storeId): ResultInterface
    {
        try {
            $request = new GetStoreRequest(store: $storeId);

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $getResponse = GetStoreResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return the full response object which contains the store data
            return new Success($getResponse);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function list(?string $continuationToken = null, ?int $pageSize = null): ResultInterface
    {
        try {
            // Normalize page size within allowed bounds
            $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

            $request = new ListStoresRequest(
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $listResponse = ListStoresResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return the full response object which includes the stores collection and continuation token
            return new Success($listResponse);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
