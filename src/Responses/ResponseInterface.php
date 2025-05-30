<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\{ClientThrowable};
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReturnTypeWillChange;

/**
 * Base interface for all OpenFGA API response objects.
 *
 * This interface establishes the foundational contract for all response objects returned
 * by the OpenFGA API. It defines the standard method for transforming raw HTTP responses
 * into structured, validated response objects that applications can work with safely.
 *
 * All concrete response implementations must provide a way to parse HTTP responses while
 * handling errors appropriately and validating data according to their specific schemas.
 *
 * @see https://openfga.dev/api/service OpenFGA API Documentation
 */
interface ResponseInterface
{
    /**
     * Create a response instance from an HTTP response.
     *
     * This method transforms a raw HTTP response from the OpenFGA API into a structured response object,
     * validating and parsing the response data according to the expected schema. It handles both successful
     * responses by parsing and validating the data, and error responses by throwing appropriate exceptions.
     *
     * @param HttpResponseInterface $response  The raw HTTP response from the OpenFGA API
     * @param HttpRequestInterface  $request   The original HTTP request that generated this response
     * @param SchemaValidator       $validator Schema validator for parsing and validating response data
     *
     * @throws ClientThrowable When network-related errors, client-side errors, or response parsing failures occur
     *
     * @return static The parsed and validated response instance containing the API response data
     *
     * @see https://openfga.dev/docs/api OpenFGA API Documentation
     */
    #[ReturnTypeWillChange]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): self;
}
