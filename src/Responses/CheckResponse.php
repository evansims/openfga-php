<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing the result of an authorization check.
 *
 * This response indicates whether a user has a specific relationship with an object,
 * along with optional resolution details explaining how the decision was reached.
 * Use this to make authorization decisions in your application.
 *
 * @see CheckResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/Check
 */
final class CheckResponse extends Response implements CheckResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new check response instance.
     *
     * @param ?bool   $allowed    Whether the check request was allowed or not
     * @param ?string $resolution The resolution explanation for the check result
     */
    public function __construct(
        private readonly ?bool $allowed = null,
        private readonly ?string $resolution = null,
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
    ): CheckResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'allowed', type: 'boolean', required: false),
                new SchemaProperty(name: 'resolution', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAllowed(): ?bool
    {
        return $this->allowed;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getResolution(): ?string
    {
        return $this->resolution;
    }
}
