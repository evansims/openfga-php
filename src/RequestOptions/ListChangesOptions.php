<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

final class ListChangesOptions extends RequestOptions
{
    public function __construct(
        public ?string $continuationToken = null,
        public ?int $pageSize = null,
    ) {
    }

    public function getQueryParameters(): array
    {
        $params = [];

        if (null !== $this->continuationToken) {
            $params['continuation_token'] = $this->continuationToken;
        }

        if (null !== $this->pageSize) {
            $params['page_size'] = $this->pageSize;
        }

        return $params;
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }
}
