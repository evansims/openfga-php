<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeZone;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Store implements StoreInterface
{
    private static ?SchemaInterface $schema = null;

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
        private readonly string $id,
        private readonly string $name,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private readonly ?DateTimeImmutable $deletedAt = null,
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
        $utcCreatedTimestamp = 0 === $this->createdAt->getOffset()
            ? $this->createdAt
            : $this->createdAt->setTimezone(new DateTimeZone('UTC'));

        $utcUpdatedTimestamp = 0 === $this->updatedAt->getOffset()
            ? $this->updatedAt
            : $this->updatedAt->setTimezone(new DateTimeZone('UTC'));

        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $utcCreatedTimestamp->format(DATE_ATOM),
            'updated_at' => $utcUpdatedTimestamp->format(DATE_ATOM),
        ];

        if (null !== $this->deletedAt) {
            $utcDeletedTimestamp = 0 === $this->deletedAt->getOffset()
                ? $this->deletedAt
                : $this->deletedAt->setTimezone(new DateTimeZone('UTC'));

            $response['deleted_at'] = $utcDeletedTimestamp->format(DATE_ATOM);
        }

        return $response;
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
                new SchemaProperty(name: 'deleted_at', type: 'datetime'),
            ],
        );
    }
}
