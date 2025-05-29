<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ListStoresRequest implements ListStoresRequestInterface
{
    public function __construct(
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
    ) {
        if (null !== $this->continuationToken && '' === $this->continuationToken) {
            throw new InvalidArgumentException('Continuation token cannot be empty');
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
        ], static fn ($v): bool => null !== $v);

        $query = [] !== $params ? '?' . http_build_query($params) : '';

        return new RequestContext(
            method: RequestMethod::GET,
            url: '/stores' . $query,
        );
    }
}
