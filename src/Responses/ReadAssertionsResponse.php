<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\{Assertion, AssertionTupleKey, TupleKey};
use OpenFGA\Models\Collections\{Assertions, AssertionsInterface, TupleKeys};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing test assertions associated with an authorization model.
 *
 * This response provides access to test assertions that validate authorization
 * model behavior. These assertions define expected outcomes for specific
 * authorization scenarios and help ensure model correctness.
 *
 * @see ReadAssertionsResponseInterface For the complete API specification
 */
final class ReadAssertionsResponse extends Response implements ReadAssertionsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new read assertions response instance.
     *
     * @param ?AssertionsInterface $assertions The collection of assertions from the authorization model, or null if none defined
     * @param string               $model      The authorization model identifier containing these assertions
     */
    public function __construct(
        private readonly ?AssertionsInterface $assertions,
        private readonly string $model,
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
    ): ReadAssertionsResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            // Register all required schemas
            $validator->registerSchema(AssertionTupleKey::schema());
            $validator->registerSchema(TupleKey::schema());
            $validator->registerSchema(TupleKeys::schema());
            $validator->registerSchema(Assertion::schema());
            $validator->registerSchema(Assertions::schema());
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
                new SchemaProperty(name: 'assertions', type: 'object', className: Assertions::class, required: false),
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true, parameterName: 'model'),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getAssertions(): ?AssertionsInterface
    {
        return $this->assertions;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }
}
