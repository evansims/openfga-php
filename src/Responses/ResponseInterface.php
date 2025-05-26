<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaValidator;
use ReturnTypeWillChange;

interface ResponseInterface
{
    #[ReturnTypeWillChange]
    public static function fromResponse(
        \Psr\Http\Message\ResponseInterface $response,
        \Psr\Http\Message\RequestInterface $request,
        SchemaValidator $validator,
    ): self;
}
