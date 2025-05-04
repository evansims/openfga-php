<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

final class GetStoreRequestOptions extends RequestOptions
{
    public function __construct(
        public ?string $continuationToken = null,
        public ?int $pageSize = null,
    ) {
    }

    public function getQueryParameters(): array
    {
        $params = [];

        if ($this->continuationToken !== null) {
            $params['continuation_token'] = $this->continuationToken;
        }

        if ($this->pageSize !== null) {
            $params['page_size'] = $this->pageSize;
        }

        return $params;
    }
}
