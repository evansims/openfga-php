<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\{Tuples, TuplesInterface};
use OpenFGA\Models\TupleInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ReadTuplesResponse extends Response implements ReadTuplesResponseInterface
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
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ReadTuplesResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(Tuples::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
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
                    new SchemaProperty(name: 'tuples', type: 'object', className: Tuples::class, required: true),
                    new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
                ],
            );
        }

        return self::$schema;
    }
}
