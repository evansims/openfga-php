<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ListTupleChangesRequest implements ListTupleChangesRequestInterface
{
    public function __construct(
        private string $store,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?string $type = null,
        private ?DateTimeImmutable $startTime = null,
    ) {
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }

        if (null !== $this->continuationToken && '' === $this->continuationToken) {
            throw new InvalidArgumentException('Continuation token cannot be empty');
        }
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
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    #[Override]
    /**
     * @inheritDoc
     */
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

    #[Override]
    /**
     * @inheritDoc
     */
    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    private static function getUtcTimestamp(?DateTimeInterface $dateTime): ?string
    {
        if (! $dateTime instanceof DateTimeInterface) {
            return null;
        }

        return ($dateTime instanceof DateTimeImmutable ? $dateTime : DateTimeImmutable::createFromInterface($dateTime))
            ->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
