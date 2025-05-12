<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

use function assert;

final class Store extends Model implements StoreInterface
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

    public function toArray(): array
    {
        $response = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
            'updated_at' => $this->updatedAt->format('Y-m-d\TH:i:s\Z'),
        ];

        if (null !== $this->deletedAt) {
            $response['deleted_at'] = $this->deletedAt->format('Y-m-d\TH:i:s\Z');
        }

        return $response;
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['id'], $data['name'], $data['created_at'], $data['updated_at']));

        return new self(
            id: $data['id'],
            name: $data['name'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
            deletedAt: isset($data['deleted_at']) ? new DateTimeImmutable($data['deleted_at']) : null,
        );
    }
}
