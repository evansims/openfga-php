<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Stores;

interface ListStoresResponseInterface extends ResponseInterface
{
    /**
     * @return string
     */
    public function getContinuationToken(): string;

    /**
     * @return Stores
     */
    public function getStores(): Stores;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
