<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Generator;
use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Schemas\SchemaValidatorInterface;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response interface for streaming objects that a user has a specific relationship with.
 *
 * This response provides a Generator that yields object identifiers as they are
 * streamed from the server. This allows for memory-efficient processing of large
 * result sets without loading the entire dataset into memory at once.
 *
 * @see https://openfga.dev/api/service#/Relationship%20Queries/StreamedListObjects
 */
interface StreamedListObjectsResponseInterface
{
    /**
     * Create a streaming response from an HTTP response.
     *
     * Processes the streaming HTTP response and returns a Generator that yields
     * individual object identifiers as they are received from the server.
     *
     * @param HttpResponseInterface    $response  The HTTP response from the API
     * @param HttpRequestInterface     $request   The original HTTP request
     * @param SchemaValidatorInterface $validator Schema validator for response validation
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or streaming fails
     *
     * @return Generator<int, StreamedListObjectsResponseInterface> Generator yielding response objects
     */
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidatorInterface $validator,
    ): Generator;

    /**
     * Get a single object identifier from a streamed response chunk.
     *
     * @return string The object identifier
     */
    public function getObject(): string;
}
