<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class GetStoreResponse implements GetStoreResponseInterface
{
    private static ?SchemaInterface $schema = null;

    use ResponseTrait;

    public function __construct(
        private string $id,
        private string $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt = null,
    ) {
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

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

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'created_at', type: 'datetime', required: true),
                new SchemaProperty(name: 'updated_at', type: 'datetime', required: true),
                new SchemaProperty(name: 'deleted_at', type: 'datetime', required: false),
            ],
        );
    }
}
