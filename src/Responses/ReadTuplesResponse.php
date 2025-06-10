<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\Collections\{ConditionParameters, Tuples, TuplesInterface};
use OpenFGA\Models\{Condition, ConditionMetadata, Tuple, TupleKey};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a paginated list of relationship tuples.
 *
 * This response provides access to relationship tuples that match the query criteria,
 * with pagination support for handling large result sets. Each tuple represents a
 * specific relationship between a user and an object.
 *
 * @see ReadTuplesResponseInterface For the complete API specification
 */
final class ReadTuplesResponse extends Response implements ReadTuplesResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new read tuples response instance.
     *
     * @param TuplesInterface $tuples            The collection of relationship tuples for the current page
     * @param string|null     $continuationToken Pagination token for fetching additional results, or null if no more pages exist
     */
    public function __construct(
        private readonly TuplesInterface $tuples,
        private readonly ?string $continuationToken = null,
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
        SchemaValidator $validator,
    ): ReadTuplesResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(ConditionMetadata::schema());
            $validator->registerSchema(ConditionParameters::schema());
            $validator->registerSchema(Condition::schema());
            $validator->registerSchema(TupleKey::schema());
            $validator->registerSchema(Tuple::schema());
            $validator->registerSchema(Tuples::schema());
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
                new SchemaProperty(name: 'tuples', type: 'object', className: Tuples::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTuples(): TuplesInterface
    {
        return $this->tuples;
    }
}
