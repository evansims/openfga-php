<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\StoresInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListStoresResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?string;

    public function getStores(): StoresInterface;

    public static function Schema(): SchemaInterface;
}
