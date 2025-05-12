<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\TupleChangesInterface;

interface ListChangesResponseInterface extends ResponseInterface
{
    /**
     * @return TupleChangesInterface
     */
    public function getChanges(): TupleChangesInterface;

    /**
     * @return ?string
     */
    public function getContinuationToken(): ?string;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
