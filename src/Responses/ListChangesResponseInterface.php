<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{ContinuationTokenInterface, TupleChangesInterface};

interface ListChangesResponseInterface extends ResponseInterface
{
    /**
     * @return TupleChangesInterface
     */
    public function getChanges(): TupleChangesInterface;

    /**
     * @return ?ContinuationTokenInterface
     */
    public function getContinuationToken(): ?ContinuationTokenInterface;
}
