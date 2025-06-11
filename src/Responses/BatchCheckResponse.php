<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{BatchCheckSingleResult, BatchCheckSingleResultInterface};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

use function is_array;
use function is_string;

/**
 * Response containing the results of a batch authorization check.
 *
 * This response contains a map of correlation IDs to check results, allowing
 * you to match each result back to the original check request using the
 * correlation ID that was provided in the batch request.
 *
 * @see BatchCheckResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
final class BatchCheckResponse extends Response implements BatchCheckResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new batch check response instance.
     *
     * @param array<string, BatchCheckSingleResultInterface> $result Map of correlation ID to check result
     */
    public function __construct(
        private readonly array $result = [],
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidatorInterface $validator,
    ): static {
        if (200 === $response->getStatusCode()) {
            $body = self::parseResponse($response, $request);

            $validator->registerSchema(self::schema());

            $result = [];

            if (isset($body['result']) && is_array($body['result'])) {
                /** @var mixed $resultData */
                foreach ($body['result'] as $correlationId => $resultData) {
                    if (is_string($correlationId) && is_array($resultData)) {
                        /** @var array<string, mixed> $resultData */
                        $result[$correlationId] = BatchCheckSingleResult::fromArray($resultData);
                    }
                }
            }

            return new self(result: $result);
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException If schema reflection fails
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'result', type: 'object', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getResultForCorrelationId(string $correlationId): ?BatchCheckSingleResultInterface
    {
        return $this->result[$correlationId] ?? null;
    }
}
