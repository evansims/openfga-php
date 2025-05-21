<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Store implements StoreInterface
{
    public const OPENAPI_MODEL = 'Store';

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly DateTimeInterface $createdAt,
        private readonly DateTimeInterface $updatedAt,
        private readonly ?DateTimeInterface $deletedAt = null,
    ) {
    }

    #[Override]
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Override]
    public function getDeletedAt(): ?DateTimeInterface
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
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => self::getUtcTimestamp($this->createdAt) ?? '',
            'updated_at' => self::getUtcTimestamp($this->updatedAt) ?? '',
            'deleted_at' => self::getUtcTimestamp($this->deletedAt),
        ], static fn ($value): bool => null !== $value);
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

    private static function getUtcTimestamp(?DateTimeInterface $dateTime): ?string
    {
        if (! $dateTime instanceof DateTimeInterface) {
            return null;
        }

        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
