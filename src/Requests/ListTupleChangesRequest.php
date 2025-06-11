<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for listing changes to relationship tuples over time.
 *
 * This request retrieves a chronological list of tuple modifications (creates, updates, deletes)
 * within a store. It's essential for auditing, change tracking, and building event-driven
 * authorization systems that react to permission changes.
 *
 * @see ListTupleChangesRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/ListTupleChanges List tuple changes API endpoint
 */
final readonly class ListTupleChangesRequest implements ListTupleChangesRequestInterface
{
    /**
     * Create a new tuple changes listing request.
     *
     * @param string                 $store             The ID of the store to list tuple changes from
     * @param string|null            $continuationToken Token for pagination to get the next page of results
     * @param int|null               $pageSize          Maximum number of changes to return per page
     * @param string|null            $type              Object type to filter changes by
     * @param DateTimeImmutable|null $startTime         Earliest time to include changes from
     *
     * @throws ClientThrowable          If the store ID is empty or the continuation token is empty (but not null)
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?string $type = null,
        private ?DateTimeImmutable $startTime = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }

        if (null !== $this->continuationToken && '' === $this->continuationToken) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_CONTINUATION_TOKEN_EMPTY)]);
        }
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
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $params = array_filter([
            'continuation_token' => $this->getContinuationToken(),
            'page_size' => $this->getPageSize(),
            'type' => $this->getType(),
            'start_time' => self::getUtcTimestamp($this->getStartTime()),
        ], static fn ($v): bool => null !== $v);

        $query = [] !== $params ? '?' . http_build_query($params) : '';

        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->store . '/changes' . $query,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
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
    public function getType(): ?string
    {
        return $this->type;
    }

    private static function getUtcTimestamp(?DateTimeInterface $dateTime): string | null
    {
        if (! $dateTime instanceof DateTimeInterface) {
            return null;
        }

        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
