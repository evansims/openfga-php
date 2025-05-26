<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\SerializationError;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

use function is_array;

abstract class Response
{
    /**
     * Parses an API JSON response body to a PHP array.
     *
     * @param ResponseInterface $response The HTTP response to parse.
     * @param RequestInterface  $request  The HTTP request associated with the response.
     *
     * @return array<mixed> The parsed response data as an associative array.
     */
    protected static function parseResponse(
        ResponseInterface $response,
        RequestInterface $request,
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
