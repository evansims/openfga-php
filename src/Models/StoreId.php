<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class StoreId extends Model implements StoreIdInterface
{
    /**
     * Constructor.
     *
     * @param string $id The store ID.
     */
    public function __construct(
        private string $id,
    ) {
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }

    public static function fromStore(Store $store): self
    {
        return new self(
            id: $store->id,
        );
    }

    public static function fromString(string $id): self
    {
        return new self(
            id: $id,
        );
    }
}
