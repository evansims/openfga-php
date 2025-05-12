<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\TuplesInterface;

interface ListTuplesResponseInterface extends ResponseInterface
{
    /**
     * @return TuplesInterface
     */
    public function getTuples(): TuplesInterface;

    /**
     * @return string
     */
    public function getContinuationToken(): string;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
