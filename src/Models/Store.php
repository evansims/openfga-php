<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents an OpenFGA authorization store that contains your permission data.
 *
 * A Store is a container for all your authorization data - the models, relationships,
 * and permission rules for a specific application or tenant. Each store is isolated
 * from others, allowing you to manage permissions for different applications or
 * environments separately.
 *
 * Think of a store as your application's dedicated permission database that holds
 * all the "who can do what" information for your system.
 */
final class Store implements StoreInterface
{
    public const string OPENAPI_MODEL = 'Store';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string             $id        The unique identifier of the store
     * @param string             $name      The display name of the store
     * @param DateTimeInterface  $createdAt The timestamp when the store was created
     * @param DateTimeInterface  $updatedAt The timestamp when the store was last updated
     * @param ?DateTimeInterface $deletedAt Optional timestamp when the store was deleted
     */
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly DateTimeInterface $createdAt,
        private readonly DateTimeInterface $updatedAt,
        private readonly ?DateTimeInterface $deletedAt = null,
    ) {
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
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDeletedAt(): ?DateTimeInterface
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
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * Converts a DateTimeInterface to a UTC timestamp string in RFC3339 format.
     *
     * @param  ?DateTimeInterface $dateTime The datetime to convert, or null
     * @return string|null        The UTC timestamp string, or null if input is null
     */
    private static function getUtcTimestamp(?DateTimeInterface $dateTime): string | null
    {
        if (! $dateTime instanceof DateTimeInterface) {
            return null;
        }

        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
