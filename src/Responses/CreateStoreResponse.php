<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response confirming successful creation of a new store.
 *
 * This response provides the details of the newly created authorization store,
 * including its unique identifier, name, and creation timestamps. Use the store
 * ID for subsequent operations like managing authorization models and tuples.
 *
 * @see CreateStoreResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Stores/CreateStore
 */
final class CreateStoreResponse extends Response implements CreateStoreResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new store creation response instance.
     *
     * @param string            $id        The unique identifier of the created store
     * @param string            $name      The human-readable name of the created store
     * @param DateTimeImmutable $createdAt The timestamp when the store was created
     * @param DateTimeImmutable $updatedAt The timestamp when the store was last updated
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
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
    ): CreateStoreResponseInterface {
        // Handle successful responses
        if (201 === $response->getStatusCode()) {
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
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
