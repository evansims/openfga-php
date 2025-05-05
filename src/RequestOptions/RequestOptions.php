<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Requests\RequestBodyFormat;

abstract class RequestOptions implements RequestOptionsInterface
{
    final public function getBodyFormat(): RequestBodyFormat
    {
        return RequestBodyFormat::JSON;
    }

    abstract public function getQueryParameters(): array;
}
