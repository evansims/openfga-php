<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints\Traits;

trait StoreIdentifiers
{
    final public function getStoreId(): ?string
    {
        if ($this->storeId === null) {
            return $this->client->getConfiguration()->getStoreId();
        }

        return $this->storeId;
    }

    final public function setStoreId(?string $storeId): self
    {
        $this->storeId = $storeId;
        return $this;
    }
}
