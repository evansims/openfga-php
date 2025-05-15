<?php

declare(strict_types=1);

namespace OpenFGA\Options;

use OpenFGA\Requests\RequestBodyFormat;

interface OptionsInterface
{
    public function getBodyFormat(): RequestBodyFormat;

    public function getQueryParameters(): array;
}
