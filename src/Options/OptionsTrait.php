<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Requests\RequestBodyFormat;

trait OptionsTrait
{
    final public function getBodyFormat(): RequestBodyFormat
    {
        return RequestBodyFormat::JSON;
    }

    final public function getQueryParameters(): array
    {
        return [];
    }
}
