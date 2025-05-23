<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Collections\{Tuples, TuplesInterface};
use OpenFGA\Models\TupleInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use Override;

use function is_array;

final class ReadTuplesResponse implements ReadTuplesResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param TuplesInterface<TupleInterface> $tuples
     * @param null|string                     $continuationToken
     */
    public function __construct(
        private TuplesInterface $tuples,
        private ?string $continuationToken = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getTuples(): TuplesInterface
    {
        return $this->tuples;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): ReadTuplesResponseInterface
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Tuples::schema());
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
        if (! self::$schema instanceof SchemaInterface) {
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
