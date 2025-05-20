<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use OpenFGA\Network\{RequestContext, RequestMethod};
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
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

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
            url: '/stores/' . $this->getStore() . '/changes' . $query,
        );
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getStore(): string
    {
        return $this->store;
    }

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
