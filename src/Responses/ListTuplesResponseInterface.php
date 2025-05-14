<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\TuplesInterface;

interface ListTuplesResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): string;

    public function getTuples(): TuplesInterface;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
