<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use DateTimeImmutable;

final class ListChangesOptions extends RequestOptions
{
    use RequestOptionsTrait;

    public function __construct(
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

    public function getQueryParameters(): array
    {
        $params = [];

        if (null !== $this->getContinuationToken()) {
            $params['continuation_token'] = $this->getContinuationToken();
        }

        if (null !== $this->getPageSize()) {
            $params['page_size'] = $this->getPageSize();
        }

        if (null !== $this->getType()) {
            $params['type'] = $this->getType();
        }

        if (null !== $this->getStartTime()) {
            $params['start_time'] = $this->getStartTime()->format('Y-m-d\TH:i:s\Z');
        }

        return $params;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
