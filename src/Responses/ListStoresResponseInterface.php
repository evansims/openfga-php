<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\StoresInterface;

interface ListStoresResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): string;

    public function getStores(): StoresInterface;
}
