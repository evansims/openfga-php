<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for reading relationship tuples that match specified criteria.
 *
 * This request retrieves tuples from a store based on filtering criteria,
 * with support for pagination and consistency levels. It's essential for
 * querying existing relationships, debugging authorization data, and building
 * administrative interfaces.
 *
 * @see ReadTuplesRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/ReadTuples Read tuples API endpoint
 */
final readonly class ReadTuplesRequest implements ReadTuplesRequestInterface
{
    /**
     * Create a new tuples reading request.
     *
     * @param string            $store             The ID of the store containing the tuples
     * @param TupleKeyInterface $tupleKey          The tuple key filter for reading specific tuples
     * @param string|null       $continuationToken Token for pagination to get the next page of results
     * @param int|null          $pageSize          Maximum number of tuples to return per page
     * @param Consistency|null  $consistency       The read consistency level for the operation
     *
     * @throws ClientThrowable          If the store ID is empty, page size is invalid, or continuation token is empty (but not null)
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?Consistency $consistency = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if (null !== $pageSize && 0 >= $pageSize) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_PAGE_SIZE_INVALID, ['className' => 'ReadTuplesRequest'])]);
        }

        if (null !== $continuationToken && '' === $continuationToken) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_CONTINUATION_TOKEN_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
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
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException If the request body cannot be serialized to JSON
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        // Only include tuple_key if it has meaningful values
        $tupleKeyData = $this->tupleKey->jsonSerialize();
        $hasMeaningfulTupleKey = (isset($tupleKeyData['user']) && '' !== $tupleKeyData['user'])
            || (isset($tupleKeyData['relation']) && '' !== $tupleKeyData['relation'])
            || (isset($tupleKeyData['object']) && '' !== $tupleKeyData['object']);

        $body = array_filter([
            'tuple_key' => $hasMeaningfulTupleKey ? $tupleKeyData : null,
            'consistency' => $this->consistency?->value,
            'page_size' => $this->pageSize,
            'continuation_token' => $this->continuationToken,
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/read',
            body: $stream,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
