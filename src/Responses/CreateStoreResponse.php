<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class CreateStoreResponse extends Response implements CreateStoreResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $id,
        private string $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
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

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): CreateStoreResponseInterface {
        // Handle successful responses
        if (201 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
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
}
