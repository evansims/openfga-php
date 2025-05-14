<?php

declare(strict_types=1);

namespace OpenFGA\RequestOptions;

use OpenFGA\Models\ContinuationTokenInterface;

final class ListModelsOptions extends RequestOptions
{
    use RequestOptionsTrait;

    public function __construct(
        private ?ContinuationTokenInterface $continuationToken = null,
        private ?int $pageSize = null,
    ) {
    }

    public function getContinuationToken(): ?ContinuationTokenInterface
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
            $params['continuation_token'] = (string) $this->getContinuationToken();
        }

        if (null !== $this->getPageSize()) {
            $params['page_size'] = $this->getPageSize();
        }

        return $params;
    }
}
