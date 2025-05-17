<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface ListStoresRequestInterface extends RequestInterface
{
    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;
}
