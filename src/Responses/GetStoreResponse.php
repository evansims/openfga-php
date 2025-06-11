<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing detailed information about a specific store.
 *
 * This response provides comprehensive store metadata including its unique identifier,
 * name, and timestamps for creation, updates, and deletion (if applicable). Use this
 * to retrieve information about an authorization store.
 *
 * @see GetStoreResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/GetStore
 */
final class GetStoreResponse extends Response implements GetStoreResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new get store response instance.
     *
     * @param string             $id        The unique identifier of the store
     * @param string             $name      The human-readable name of the store
     * @param DateTimeImmutable  $createdAt The timestamp when the store was created
     * @param DateTimeImmutable  $updatedAt The timestamp when the store was last updated
     * @param ?DateTimeImmutable $deletedAt The timestamp when the store was deleted, if applicable
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private readonly ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidatorInterface $validator,
    ): GetStoreResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

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
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'created_at', type: 'string', format: 'datetime', required: true),
                new SchemaProperty(name: 'updated_at', type: 'string', format: 'datetime', required: true),
                new SchemaProperty(name: 'deleted_at', type: 'string', format: 'datetime', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): StoreInterface
    {
        return new Store(
            id: $this->getId(),
            name: $this->getName(),
            createdAt: $this->getCreatedAt(),
            updatedAt: $this->getUpdatedAt(),
            deletedAt: $this->getDeletedAt(),
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
