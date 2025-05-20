<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\StoresInterface;
use OpenFGA\Models\StoreInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListStoresResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?string;

    /**
     * @return StoresInterface<StoreInterface>
     */
    public function getStores(): StoresInterface;

    public static function schema(): SchemaInterface;
}
