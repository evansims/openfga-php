<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTime;

final class Store extends Model implements StoreInterface
{
    /**
     * Constructor.
     *
     * @param string $id The store id.
     * @param string $name The store name.
     * @param DateTime $createdAt The store creation date.
     * @param DateTime $updatedAt The store update date.
     * @param DateTime|null $deletedAt The store deletion date.
     */
    public function __construct(
        public string $id,
        public string $name,
        public DateTime $createdAt,
        public DateTime $updatedAt,
        public ?DateTime $deletedAt = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:sP'),
            'updated_at' => $this->updatedAt->format('Y-m-d\TH:i:sP'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d\TH:i:sP'),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            createdAt: new DateTime($data['created_at']),
            updatedAt: new DateTime($data['updated_at']),
            deletedAt: isset($data['deleted_at']) ? new DateTime($data['deleted_at']) : null,
        );
    }
}
