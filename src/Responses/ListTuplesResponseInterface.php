<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{ContinuationTokenInterface, TuplesInterface};

interface ListTuplesResponseInterface extends ResponseInterface
{
    public function getContinuationToken(): ?ContinuationTokenInterface;

    public function getTuples(): TuplesInterface;
}
