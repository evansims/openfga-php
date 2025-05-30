<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\{Stores, StoresInterface};
use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ListStoresResponse extends Response implements ListStoresResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param StoresInterface<StoreInterface> $stores
     * @param ?string                         $continuationToken
     */
    public function __construct(
        private StoresInterface $stores,
        private ?string $continuationToken = null,
    ) {
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

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListStoresResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(Store::schema());
            $validator->registerSchema(Stores::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
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
}
