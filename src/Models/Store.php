<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Store implements StoreInterface
{
    /**
     * Constructor.
     *
     * @param string                 $id        The store id.
     * @param string                 $name      The store name.
     * @param DateTimeImmutable      $createdAt The store creation date.
     * @param DateTimeImmutable      $updatedAt The store update date.
     * @param null|DateTimeImmutable $deletedAt The store deletion date.
     */
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

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function jsonSerialize(): array
    {
        $response = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d\TH:i:s\Z'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d\TH:i:s\Z'),
        ];

        if (null !== $this->getDeletedAt()) {
            $response['deleted_at'] = $this->getDeletedAt()->format('Y-m-d\TH:i:s\Z');
        }

        return $response;
    }

    public static function Schema(): SchemaInterface
    {
        return new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'name', type: 'string', required: true),
                new SchemaProperty(name: 'created_at', type: 'datetime', required: true),
                new SchemaProperty(name: 'updated_at', type: 'datetime', required: true),
                new SchemaProperty(name: 'deleted_at', type: 'datetime'),
            ],
        );
    }
}
