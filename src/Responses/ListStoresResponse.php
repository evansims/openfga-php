<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Collections\{Stores, StoresInterface};
use OpenFGA\Models\StoreInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use function is_array;

final class ListStoresResponse implements ListStoresResponseInterface
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

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public function getStores(): StoresInterface
    {
        return $this->stores;
    }

    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Stores::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'stores', type: Stores::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }
}
