<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Tuples, TuplesInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

/**
 * @implements ReadTuplesResponseInterface<array{tuples: array, continuation_token: string|null}>
 */
final class ReadTuplesResponse implements ReadTuplesResponseInterface
{
    use ResponseTrait;

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private TuplesInterface $tuples,
        private ?string $continuationToken = null,
    ) {
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public function getTuples(): TuplesInterface
    {
        return $this->tuples;
    }

    /**
     * @return array{tuples: array<array{key: array{user: string, relation: string, object: string}, timestamp: string}>, continuation_token: null|string}
     */
    public function toArray(): array
    {
        $tuples = [];

        foreach ($this->tuples as $tuple) {
            if (null === $tuple) {
                continue;
            }

            $key = $tuple->getKey();
            $timestamp = $tuple->getTimestamp();

            // Get values from the key
            $user = $key->getUser() ?? '';
            $relation = $key->getRelation() ?? '';
            $object = $key->getObject() ?? '';

            // Format the timestamp
            $timestampStr = $timestamp->format('Y-m-d\TH:i:s.u\Z');

            $tuples[] = [
                'key' => [
                    'user' => $user,
                    'relation' => $relation,
                    'object' => $object,
                ],
                'timestamp' => $timestampStr,
            ];
        }

        return [
            'tuples' => $tuples,
            'continuation_token' => $this->continuationToken,
        ];
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
            $validator->registerSchema(Tuples::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        if (null === self::$schema) {
            self::$schema = new Schema(
                className: self::class,
                properties: [
                    new SchemaProperty(name: 'tuples', type: Tuples::class, required: true),
                    new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
                ],
            );
        }

        return self::$schema;
    }
}
