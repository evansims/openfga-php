<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{ContinuationTokenInterface, StoresInterface};

interface ListStoresResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?ContinuationTokenInterface;

    public function getStores(): StoresInterface;
}
