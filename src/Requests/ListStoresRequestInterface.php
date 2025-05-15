<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Options\ListStoresOptionsInterface;

interface ListStoresRequestInterface extends RequestInterface
{
    public function getOptions(): ?ListStoresOptionsInterface;
}
