<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use const FNM_CASEFOLD;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable, NetworkError};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\Stores;
use OpenFGA\Models\Store;
use OpenFGA\Repositories\StoreRepositoryInterface;
use OpenFGA\Responses\ListStoresResponse;
use OpenFGA\Results\{Failure, FailureInterface, Success, SuccessInterface};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;
use Throwable;

use function fnmatch;
use function mb_strlen;
use function sprintf;
use function str_contains;
use function trim;

/**
 * Service implementation for high-level store operations.
 *
 * This service provides business-focused abstractions over the StoreRepository,
 * adding validation, convenience methods, and enhanced error messages. It handles
 * common store management patterns while maintaining consistency with the SDK's
 * Result pattern for error handling.
 *
 * The service is designed to simplify store operations for application developers
 * by providing intuitive methods that handle edge cases and provide clear feedback.
 *
 * @see StoreServiceInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores Store management documentation
 */
final readonly class StoreService implements StoreServiceInterface
{
    /**
     * Maximum allowed length for store names.
     */
    private const int MAX_STORE_NAME_LENGTH = 256;

    /**
     * Create a new store service instance.
     *
     * @param StoreRepositoryInterface $storeRepository The repository for store operations
     */
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function createStore(string $name): FailureInterface | SuccessInterface
    {
        $name = trim($name);

        // Validate store name
        if ('' === $name) {
            return new Failure(
                ClientError::Validation->exception(
                    context: ['message' => Translator::trans(Messages::STORE_NAME_REQUIRED)],
                ),
            );
        }

        if (self::MAX_STORE_NAME_LENGTH < mb_strlen($name)) {
            return new Failure(
                ClientError::Validation->exception(
                    context: [
                        'message' => sprintf(
                            Translator::trans(Messages::STORE_NAME_TOO_LONG),
                            self::MAX_STORE_NAME_LENGTH,
                            mb_strlen($name),
                        ),
                    ],
                ),
            );
        }

        $result = $this->storeRepository->create($name);

        return $result instanceof FailureInterface || $result instanceof SuccessInterface ? $result : new Failure(ClientError::Serialization->exception(context: ['message' => 'Unexpected result type']));
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function deleteStore(string $storeId, bool $confirmExists = true): FailureInterface | SuccessInterface
    {
        if ($confirmExists) {
            $findResult = $this->findStore($storeId);

            if ($findResult instanceof FailureInterface) {
                return $findResult;
            }
        }

        $result = $this->storeRepository->delete($storeId);

        return $result instanceof FailureInterface || $result instanceof SuccessInterface ? $result : new Failure(ClientError::Serialization->exception(context: ['message' => 'Unexpected result type']));
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function findStore(string $storeId): FailureInterface | SuccessInterface
    {
        $result = $this->storeRepository->get($storeId);

        $finalResult = $result instanceof FailureInterface || $result instanceof SuccessInterface ? $result : new Failure(ClientError::Serialization->exception(context: ['message' => 'Unexpected result type']));

        $recoveredResult = $finalResult->recover(static function (Throwable $error): Failure {
            // Enhance error messages for common cases
            $message = $error->getMessage();

            if (str_contains($message, '404') || str_contains($message, 'not found')) {
                return new Failure(
                    NetworkError::UndefinedEndpoint->exception(
                        context: [
                            'message' => sprintf(
                                Translator::trans(Messages::STORE_NOT_FOUND),
                                'with the specified ID',
                            ),
                        ],
                    ),
                );
            }

            return new Failure($error);
        });

        return $recoveredResult instanceof FailureInterface || $recoveredResult instanceof SuccessInterface ? $recoveredResult : new Failure(ClientError::Serialization->exception(context: ['message' => 'Unexpected result type']));
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function findStoresByName(string $pattern, ?int $maxItems = null): FailureInterface | SuccessInterface
    {
        $listResult = $this->listAllStores();

        if ($listResult instanceof FailureInterface) {
            return $listResult;
        }

        $stores = $listResult->unwrap();

        if (! $stores instanceof Stores) {
            return new Failure(
                ClientError::Serialization->exception(
                    context: ['message' => Translator::trans(Messages::RESPONSE_UNEXPECTED_TYPE)],
                ),
            );
        }

        $matchingStores = new Stores;
        $itemsFound = 0;

        foreach ($stores as $store) {
            // Use fnmatch for wildcard pattern matching
            if (fnmatch($pattern, $store->getName(), FNM_CASEFOLD)) {
                if (null !== $maxItems && $itemsFound >= $maxItems) {
                    break;
                }

                $matchingStores->add($store);
                ++$itemsFound;
            }
        }

        return new Success($matchingStores);
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function getOrCreateStore(string $name): FailureInterface | SuccessInterface
    {
        $name = trim($name);

        // First, try to find an existing store with this name
        $listResult = $this->listAllStores();

        if ($listResult instanceof FailureInterface) {
            return $listResult;
        }

        $stores = $listResult->unwrap();

        if (! $stores instanceof Stores) {
            return new Failure(
                ClientError::Serialization->exception(
                    context: ['message' => Translator::trans(Messages::RESPONSE_UNEXPECTED_TYPE)],
                ),
            );
        }

        // Look for a store with the exact name
        foreach ($stores as $store) {
            if ($store->getName() === $name) {
                return new Success($store);
            }
        }

        // No existing store found, create a new one
        return $this->createStore($name);
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If validation or serialization errors occur
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws Throwable                If result unwrapping fails
     */
    #[Override]
    public function listAllStores(?int $maxItems = null): FailureInterface | SuccessInterface
    {
        $allStores = new Stores;
        $continuationToken = null;
        $itemsRetrieved = 0;

        do {
            $result = $this->storeRepository->list($continuationToken);

            if ($result instanceof FailureInterface) {
                return $result;
            }

            $response = $result->unwrap();

            if (! $response instanceof ListStoresResponse) {
                return new Failure(
                    ClientError::Serialization->exception(
                        context: ['message' => Translator::trans(Messages::RESPONSE_UNEXPECTED_TYPE)],
                    ),
                );
            }

            foreach ($response->getStores() as $store) {
                if (null !== $maxItems && $itemsRetrieved >= $maxItems) {
                    return new Success($allStores);
                }

                $allStores->add($store);
                ++$itemsRetrieved;
            }

            $continuationToken = $response->getContinuationToken();
        } while (null !== $continuationToken && (null === $maxItems || $itemsRetrieved < $maxItems));

        return new Success($allStores);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function listStores(?string $continuationToken = null, ?int $pageSize = null): FailureInterface | SuccessInterface
    {
        $result = $this->storeRepository->list($continuationToken, $pageSize);

        return $result instanceof FailureInterface || $result instanceof SuccessInterface ? $result : new Failure(ClientError::Serialization->exception(context: ['message' => 'Unexpected result type']));
    }
}
