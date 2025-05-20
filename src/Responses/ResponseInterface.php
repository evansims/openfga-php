<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaValidator;

interface ResponseInterface
{
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static;
}
