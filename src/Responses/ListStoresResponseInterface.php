<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Stores;

interface ListStoresResponseInterface extends ResponseInterface
{
    /**
     * @return Stores
     */
    public function getStores(): Stores;

    /**
     * @return string
     */
    public function getContinuationToken(): string;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
