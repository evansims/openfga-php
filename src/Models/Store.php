<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;
use InvalidArgumentException;

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

    public static function fromArray(array $data): self
    {
        $data = self::validatedStoreShape($data);

        return new self(
            id: $data['id'],
            name: $data['name'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
            deletedAt: isset($data['deleted_at']) ? new DateTimeImmutable($data['deleted_at']) : null,
        );
    }

    /**
     * @param array{id: string, name: string, created_at: string, updated_at: string, deleted_at?: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return StoreShape
     */
    public static function validatedStoreShape(array $data): array
    {
        if (! isset($data['id'], $data['name'], $data['created_at'], $data['updated_at'])) {
            throw new InvalidArgumentException('Invalid store data');
        }

        return $data;
    }
}
