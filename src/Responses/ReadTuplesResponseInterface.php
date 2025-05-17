<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\TuplesInterface;
use OpenFGA\Schema\SchemaInterface;

/**
 * @template T of array
 *
 * @extends ResponseInterface<T>
 */
interface ReadTuplesResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?string;

    public function getTuples(): TuplesInterface;

    public static function schema(): SchemaInterface;
}
