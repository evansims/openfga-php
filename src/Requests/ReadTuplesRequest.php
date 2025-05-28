<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class ReadTuplesRequest implements ReadTuplesRequestInterface
{
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?string $continuationToken = null,
        private ?int $pageSize = null,
        private ?Consistency $consistency = null,
    ) {
        assert($this->store !== '', new InvalidArgumentException('Store ID cannot be empty'));

        if (null !== $pageSize && $pageSize <= 0) {
            throw new InvalidArgumentException('$pageSize must be a positive integer.');
        }

        if (null !== $continuationToken && '' === $continuationToken) {
            throw new InvalidArgumentException('$continuationToken cannot be an empty string.');
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
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
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'consistency' => $this->consistency?->value,
            'page_size' => $this->pageSize,
            'continuation_token' => $this->continuationToken,
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/read',
            body: $stream,
        );
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
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
