<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\TuplesInterface;
use OpenFGA\Models\TupleInterface;
use OpenFGA\Schema\SchemaInterface;

interface ReadTuplesResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?string;

    /**
     * @return TuplesInterface<TupleInterface>
     */
    public function getTuples(): TuplesInterface;

    public static function schema(): SchemaInterface;
}
