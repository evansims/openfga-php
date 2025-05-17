<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Models\{Consistency, TupleKeyInterface};
use OpenFGA\Network\{RequestContext, RequestMethod};
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
        if (null !== $pageSize && $pageSize <= 0) {
            throw new InvalidArgumentException('$pageSize must be a positive integer.');
        }

        if (null !== $continuationToken && '' === $continuationToken) {
            throw new InvalidArgumentException('$continuationToken cannot be an empty string.');
        }
    }

    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
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
        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'consistency' => $this->consistency?->value,
            'page_size' => $this->pageSize,
            'continuation_token' => $this->continuationToken,
        ], static fn ($value) => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/read',
            body: $stream,
        );
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
