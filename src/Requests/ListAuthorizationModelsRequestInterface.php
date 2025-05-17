<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

interface ListAuthorizationModelsRequestInterface extends RequestInterface
{
    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;

    public function getStore(): string;
}
