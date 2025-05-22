<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use ReturnTypeWillChange;
use OpenFGA\Schema\SchemaValidator;

interface ResponseInterface
{
    #[ReturnTypeWillChange]
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): ResponseInterface;
}
