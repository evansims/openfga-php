<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Requests\RequestBodyFormat;

interface RequestOptionsInterface
{
    public function getBodyFormat(): RequestBodyFormat;

    public function getQueryParameters(): array;
}
