<?php

declare(strict_types=1);

namespace OpenFGA\Options;

interface ListAuthorizationModelsOptionsInterface extends OptionsInterface
{
    public function getContinuationToken(): ?string;

    public function getPageSize(): ?int;
}
