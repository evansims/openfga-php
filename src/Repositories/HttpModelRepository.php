<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Requests\{CreateAuthorizationModelRequest, GetAuthorizationModelRequest, ListAuthorizationModelsRequest};
use OpenFGA\Responses\{CreateAuthorizationModelResponse, GetAuthorizationModelResponse, ListAuthorizationModelsResponse};
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\HttpServiceInterface;
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\RequestInterface;
use ReflectionException;
use Throwable;

use function max;
use function min;

/**
 * HTTP implementation of the model repository.
 *
 * This repository handles authorization model operations via HTTP requests to the OpenFGA API.
 * It converts domain objects to API requests, sends them via the HTTP service,
 * and transforms responses back to domain objects. Supports creating, retrieving,
 * and listing authorization models within a store.
 *
 * Authorization models define the permission structure for your application - the types
 * of objects, the relationships between them, and the rules that govern access. Models
 * are immutable once created; to update permissions, you create a new model version.
 *
 * @see ModelRepositoryInterface For the domain contract
 * @see https://openfga.dev/docs/api#/Authorization%20Models Authorization model operations documentation
 */
final readonly class HttpModelRepository implements ModelRepositoryInterface
{
    private const int MAX_PAGE_SIZE = 100;

    /**
     * @param HttpServiceInterface $httpService Service for making HTTP requests
     * @param SchemaValidator      $validator   Validator for API responses
     * @param string               $storeId     The store ID for model operations
     *
     * @throws ClientThrowable          If the store ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private HttpServiceInterface $httpService,
        private SchemaValidator $validator,
        private string $storeId,
    ) {
        if ('' === $this->storeId) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function create(
        TypeDefinitionsInterface $typeDefinitions,
        SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        ?ConditionsInterface $conditions = null,
    ): FailureInterface | SuccessInterface {
        try {
            $request = new CreateAuthorizationModelRequest(
                store: $this->storeId,
                typeDefinitions: $typeDefinitions,
                schemaVersion: $schemaVersion,
                conditions: $conditions,
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $createResponse = CreateAuthorizationModelResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return the create response which contains the model ID
            return new Success($createResponse);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function get(string $modelId): FailureInterface | SuccessInterface
    {
        try {
            $request = new GetAuthorizationModelRequest(
                store: $this->storeId,
                model: $modelId,
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $getResponse = GetAuthorizationModelResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return the full response object which contains the model data
            return new Success($getResponse);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function list(?int $pageSize = null, ?string $continuationToken = null): FailureInterface | SuccessInterface
    {
        try {
            // Normalize page size to be within allowed bounds
            $pageSize = null !== $pageSize ? max(1, min($pageSize, self::MAX_PAGE_SIZE)) : null;

            $request = new ListAuthorizationModelsRequest(
                store: $this->storeId,
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );

            $response = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw ClientError::Network->exception(context: ['message' => 'Failed to capture HTTP request']);
            }

            $listResponse = ListAuthorizationModelsResponse::fromResponse(
                $response,
                $lastRequest,
                $this->validator,
            );

            // Return the full response object which includes both models and continuation token
            return new Success($listResponse);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
