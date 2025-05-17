<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\{SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

/**
 * @template T of array
 */
interface ResponseInterface
{
    /**
     * @return T
     */
    public function toArray(): array;

    /**
     * @param HttpResponseInterface $response
     * @param SchemaValidator       $validator
     *
     * @return static<T>
     */
    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static;

    public static function handleResponseException(HttpResponseInterface $response): void;
}
