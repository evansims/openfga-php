<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    /**
     * @param string                                     $store
     * @param string                                     $model
     * @param null|TupleKeysInterface<TupleKeyInterface> $writes
     * @param null|TupleKeysInterface<TupleKeyInterface> $deletes
     */
    public function __construct(
        private string $store,
        private string $model,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
    ) {
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }
        if ('' === $this->model) {
            throw new InvalidArgumentException('Authorization model ID cannot be empty');
        }
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getModel(): string
    {
        return $this->model;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'authorization_model_id' => $this->model,
            'writes' => $this->writes?->jsonSerialize(),
            'deletes' => $this->deletes?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/write',
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
    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }
}
