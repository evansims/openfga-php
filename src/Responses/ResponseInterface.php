<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\{SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface
{
    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static;

    public static function handleResponseException(HttpResponseInterface $response): void;
}
