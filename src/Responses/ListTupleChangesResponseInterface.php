<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\TupleChangesInterface;
use OpenFGA\Models\TupleChangeInterface;
use OpenFGA\Schema\SchemaInterface;

interface ListTupleChangesResponseInterface extends ResponseInterface
{
    /**
     * @return TupleChangesInterface<TupleChangeInterface>
     */
    public function getChanges(): TupleChangesInterface;

    public function getContinuationToken(): ?string;

    public static function schema(): SchemaInterface;
}
