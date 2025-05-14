<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\StoreInterface;

interface GetStoreResponseInterface extends ResponseInterface
{
    /**
     * @return StoreInterface
     */
    public function getStore(): StoreInterface;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
