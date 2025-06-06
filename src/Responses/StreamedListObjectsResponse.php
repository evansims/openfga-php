<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use const JSON_THROW_ON_ERROR;

use Generator;
use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientThrowable, NetworkException, SerializationError, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\SchemaValidator;
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

use function is_array;
use function is_string;
use function json_decode;
use function strpos;
use function substr;
use function trim;

/**
 * Response containing streaming objects that a user has a specific relationship with.
 *
 * This response processes a streaming HTTP response and yields object identifiers
 * as they are received from the server. This allows for memory-efficient processing
 * of large result sets without loading the entire dataset into memory.
 *
 * @see StreamedListObjectsResponseInterface For the complete API specification
 * @see https://openfga.dev/api/service#/Relationship%20Queries/StreamedListObjects
 */
final readonly class StreamedListObjectsResponse implements StreamedListObjectsResponseInterface
{
    /**
     * Create a new streamed list objects response instance.
     *
     * @param string $object The object identifier from the streamed response
     */
    public function __construct(
        private string $object,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the response handling fails
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or streaming fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): Generator {
        // Handle network errors first
        if (200 !== $response->getStatusCode()) {
            RequestManager::handleResponseException(
                response: $response,
                request: $request,
            );
        }

        // Handle successful responses
        $body = $response->getBody();

        // Process the stream line by line
        $buffer = '';

        while (! $body->eof()) {
            $chunk = $body->read(1024); // Read in chunks for better performance
            $buffer .= $chunk;

            // Process complete lines
            while (false !== ($pos = strpos($buffer, "\n"))) {
                $line = substr($buffer, 0, $pos);
                $buffer = substr($buffer, $pos + 1);

                $line = trim($line);

                if ('' === $line) {
                    continue;
                }

                try {
                    /** @var array<string, mixed>|null $data */
                    $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                    // Check if this line contains a result
                    if (is_array($data) && isset($data['result']) && is_array($data['result']) && isset($data['result']['object']) && is_string($data['result']['object'])) {
                        yield new self($data['result']['object']);
                    }
                } catch (JsonException $e) {
                    throw SerializationError::Response->exception(request: $request, response: $response, context: ['line' => $line, 'error' => $e->getMessage(), ], prev: $e, );
                }
            }
        }

        // Process any remaining data in buffer
        $line = trim($buffer);

        if ('' !== $line) {
            try {
                /** @var array<string, mixed>|null $data */
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                // Check if this line contains a result
                if (is_array($data) && isset($data['result']) && is_array($data['result']) && isset($data['result']['object']) && is_string($data['result']['object'])) {
                    yield new self($data['result']['object']);
                }
            } catch (JsonException $e) {
                throw SerializationError::Response->exception(request: $request, response: $response, context: ['line' => $line, 'error' => $e->getMessage(), ], prev: $e, );
            }
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): string
    {
        return $this->object;
    }
}
