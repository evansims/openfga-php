<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Store;

interface GetStoreResponseInterface extends ResponseInterface
{
    /**
     * @return Store
     */
    public function getStore(): Store;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
