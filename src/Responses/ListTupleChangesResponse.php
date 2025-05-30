<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Models\Collections\{TupleChanges, TupleChangesInterface};
use OpenFGA\Models\{TupleChange, TupleChangeInterface, TupleKey};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a paginated list of tuple changes from the store.
 *
 * This response provides a collection of tuple changes (additions, deletions) along
 * with pagination information for retrieving additional pages of results. Use this
 * to track the history of relationship changes in your authorization store.
 *
 * @see ListTupleChangesResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/ReadChanges
 */
final class ListTupleChangesResponse extends Response implements ListTupleChangesResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new list tuple changes response instance.
     *
     * @param TupleChangesInterface<TupleChangeInterface> $changes           The collection of tuple changes for the current page
     * @param ?string                                     $continuationToken Pagination token for fetching additional results, or null if no more pages exist
     */
    public function __construct(
        private readonly TupleChangesInterface $changes,
        private readonly ?string $continuationToken,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws ClientThrowable          If the response format is invalid or status code indicates an error
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws JsonException            If the response body is not valid JSON
     * @throws ReflectionException      If exception location capture fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidator $validator,
    ): ListTupleChangesResponseInterface {
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(TupleKey::schema());
            $validator->registerSchema(TupleChange::schema());
            $validator->registerSchema(TupleChanges::schema());
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
                new SchemaProperty(name: 'changes', type: 'object', className: TupleChanges::class, required: true),
                new SchemaProperty(name: 'continuation_token', type: 'string', required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getChanges(): TupleChangesInterface
    {
        return $this->changes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }
}
