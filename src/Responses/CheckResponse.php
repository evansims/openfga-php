<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class CheckResponse extends Response implements CheckResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?bool $allowed = null,
        private ?string $resolution = null,
    ) {
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

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): CheckResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

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
                new SchemaProperty(name: 'allowed', type: 'boolean', required: false),
                new SchemaProperty(name: 'resolution', type: 'string', required: false),
            ],
        );
    }
}
