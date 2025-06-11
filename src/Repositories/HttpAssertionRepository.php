<?php

declare(strict_types=1);

namespace OpenFGA\Repositories;

use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Requests\{ReadAssertionsRequest, WriteAssertionsRequest};
use OpenFGA\Responses\{ReadAssertionsResponse, WriteAssertionsResponse};
use OpenFGA\Results\{Failure, Success, SuccessInterface};
use OpenFGA\Schemas\SchemaValidatorInterface;
use OpenFGA\Services\HttpServiceInterface;
use Override;
use Psr\Http\Message\RequestInterface;
use RuntimeException;
use Throwable;

/**
 * HTTP implementation of assertion repository for OpenFGA API communication.
 *
 * This repository handles assertion operations by communicating with the OpenFGA
 * HTTP API. It transforms business operations into HTTP requests and responses,
 * handling serialization, deserialization, and error management.
 *
 * @see AssertionRepositoryInterface Repository interface
 * @see HttpServiceInterface HTTP service dependency
 */
final readonly class HttpAssertionRepository implements AssertionRepositoryInterface
{
    /**
     * Create a new HTTP assertion repository instance.
     *
     * @param HttpServiceInterface     $httpService HTTP service for API communication
     * @param SchemaValidatorInterface $validator   Schema validator for response validation
     * @param string                   $storeId     Store ID for scoped operations
     */
    public function __construct(
        private HttpServiceInterface $httpService,
        private SchemaValidatorInterface $validator,
        private string $storeId,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function read(string $authorizationModelId): Failure | Success | SuccessInterface
    {
        try {
            $request = new ReadAssertionsRequest(
                store: $this->storeId,
                model: $authorizationModelId,
            );

            $httpResponse = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw new RuntimeException('No HTTP request available');
            }

            $response = ReadAssertionsResponse::fromResponse(
                $httpResponse,
                $lastRequest,
                $this->validator,
            );

            return new Success($response);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function write(string $authorizationModelId, AssertionsInterface $assertions): Failure | Success | SuccessInterface
    {
        try {
            $request = new WriteAssertionsRequest(
                assertions: $assertions,
                store: $this->storeId,
                model: $authorizationModelId,
            );

            $httpResponse = $this->httpService->send($request);

            $lastRequest = $this->httpService->getLastRequest();

            if (! $lastRequest instanceof RequestInterface) {
                throw new RuntimeException('No HTTP request available');
            }

            WriteAssertionsResponse::fromResponse(
                $httpResponse,
                $lastRequest,
                $this->validator,
            );

            return new Success(true);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
