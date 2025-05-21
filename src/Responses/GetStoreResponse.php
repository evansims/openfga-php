<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use Override;

use function is_array;

final class GetStoreResponse implements GetStoreResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private string $id,
        private string $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    #[Override]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Override]
    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    #[Override]
    public function getId(): string
    {
        return $this->id;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

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

    #[Override]
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[Override]
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'created_at', type: 'string', format: 'date-time', required: true),
                new SchemaProperty(name: 'updated_at', type: 'string', format: 'date-time', required: true),
                new SchemaProperty(name: 'deleted_at', type: 'string', format: 'date-time', required: false),
            ],
        );
    }
}
