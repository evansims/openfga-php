<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use const JSON_THROW_ON_ERROR;

use Exception;
use InvalidArgumentException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Exceptions\{SerializationError, SerializationException};
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

use function is_array;

/**
 * Base class for all OpenFGA API response objects.
 *
 * This abstract class provides common functionality for parsing and handling
 * HTTP responses from the OpenFGA API. It includes utilities for JSON response
 * parsing with proper error handling and context preservation.
 *
 * @see ResponseInterface For the response interface specification
 */
abstract class Response
{
    /**
     * Parses an API JSON response body to a PHP array.
     *
     * This method extracts the JSON response body from an HTTP response and parses it into
     * a PHP associative array. It handles JSON parsing errors by throwing appropriate
     * serialization exceptions with context about the failed request and response.
     *
     * @param HttpResponseInterface $response The HTTP response to parse
     * @param HttpRequestInterface  $request  The HTTP request associated with the response
     *
     * @throws ClientThrowable          If the response cannot be processed
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   When JSON parsing fails or response data is invalid
     *
     * @return array<mixed> The parsed response data as an associative array, or empty array if data is not an array
     */
    protected static function parseResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
    ): array {
        try {
            $json = (string) $response->getBody();
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            // Handle unparseable response
            throw SerializationError::Response->exception(request: $request, response: $response, prev: $exception);
        }

        if (! is_array($data)) {
            return [];
        }

        return $data;
    }
}
