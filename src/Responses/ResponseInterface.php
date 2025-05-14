<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface
{
    /**
     * @param HttpResponseInterface $response
     */
    public static function fromResponse(HttpResponseInterface $response): static;

    /**
     * @param HttpResponseInterface $response
     */
    public static function handleResponseException(HttpResponseInterface $response): void;
}
