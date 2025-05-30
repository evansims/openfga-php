<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{Assertion, AssertionInterface, AssertionTupleKey, TupleKey};
use OpenFGA\Models\Collections\{Assertions, AssertionsInterface, TupleKeys};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ReadAssertionsResponse extends Response implements ReadAssertionsResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param AssertionsInterface<AssertionInterface> $assertions
     * @param string                                  $model
     */
    public function __construct(
        private ?AssertionsInterface $assertions,
        private string $model,
    ) {
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

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ReadAssertionsResponseInterface {
        // Handle successful responses
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

        // Handle network errors
        return RequestManager::handleResponseException(
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
}
