<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ListAuthorizationModelsRequest implements ListAuthorizationModelsRequestInterface
{
    public function __construct(
        private string $store,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
    ) {
    }

    #[Override]
    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    #[Override]
    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $params = array_filter([
            'continuation_token' => $this->getContinuationToken(),
            'page_size' => $this->getPageSize(),
        ], static fn ($v): bool => null !== $v);

        $query = [] !== $params ? '?' . http_build_query($params) : '';

        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores/' . $this->getStore() . '/authorization-models' . $query,
        );
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }
}
