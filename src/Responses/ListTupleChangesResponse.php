<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{TupleChanges, TupleChangesInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ListTupleChangesResponse implements ListTupleChangesResponseInterface
{
    private static ?SchemaInterface $schema = null;

    use ResponseTrait;

    public function __construct(
        private TupleChangesInterface $changes,
        private ?string $continuationToken,
    ) {
    }

    public function getChanges(): TupleChangesInterface
    {
        return $this->changes;
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(TupleChanges::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'changes', type: TupleChanges::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }
}
