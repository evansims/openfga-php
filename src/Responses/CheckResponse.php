<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use Override;

use function is_array;

final class CheckResponse implements CheckResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?bool $allowed = null,
        private ?string $resolution = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getAllowed(): ?bool
    {
        return $this->allowed;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    #[Override]
    /**
     * @inheritDoc
     */
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
