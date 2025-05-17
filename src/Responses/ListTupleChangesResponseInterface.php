<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\TupleChangesInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListTupleChangesResponseInterface extends ResponseInterface
{
    public function getChanges(): TupleChangesInterface;

    public function getContinuationToken(): ?string;

    public static function schema(): SchemaInterface;
}
