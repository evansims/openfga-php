<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\StoresInterface;

interface ListStoresResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): string;

    public function getStores(): StoresInterface;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
