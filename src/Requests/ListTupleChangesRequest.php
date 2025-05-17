<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use DateTimeImmutable;
use DateTimeZone;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Psr\Http\Message\StreamFactoryInterface;

use function count;

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
            'start_time' => $this->getStartTime()?->setTimezone(new DateTimeZone('UTC'))->format(DATE_ATOM),
        ], static fn ($v) => null !== $v);

        $query = count($params) > 0 ? '?' . http_build_query($params) : '';

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
}
