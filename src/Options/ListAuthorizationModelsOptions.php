<?php

declare(strict_types=1);

namespace OpenFGA\Options;

final class ListAuthorizationModelsOptions implements ListAuthorizationModelsOptionsInterface
{
    use OptionsTrait;

    public function __construct(
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
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
            $params['continuation_token'] = (string) $this->getContinuationToken();
        }

        if (null !== $this->getPageSize()) {
            $params['page_size'] = $this->getPageSize();
        }

        return $params;
    }
}
