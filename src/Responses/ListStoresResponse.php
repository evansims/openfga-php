<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\Collections\{Stores, StoresInterface};
use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a paginated list of available stores.
 *
 * This response provides access to stores that the authenticated user or application
 * can access, with pagination support for handling large numbers of stores. Each
 * store includes its ID, name, and creation metadata.
 *
 * @see ListStoresResponseInterface For the complete API specification
 */
final class ListStoresResponse extends Response implements ListStoresResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new list stores response instance.
     *
     * @param StoresInterface<StoreInterface> $stores            The collection of stores for the current page
     * @param ?string                         $continuationToken Pagination token for fetching additional results, or null if no more pages exist
     */
    public function __construct(
        private readonly StoresInterface $stores,
        private readonly ?string $continuationToken = null,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the response format is invalid or status code indicates an error
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws JsonException            If the response body is not valid JSON
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): ListStoresResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(Store::schema());
            $validator->registerSchema(Stores::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'stores', type: 'object', className: Stores::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStores(): StoresInterface
    {
        return $this->stores;
    }
}
