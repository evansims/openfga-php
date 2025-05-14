<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class StoreId implements StoreIdInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function __toString(): string
    {
        return $this->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): string
    {
        return $this->getId();
    }

    public static function fromStore(Store $store): self
    {
        return new self(
            id: $store->getId(),
        );
    }
}
