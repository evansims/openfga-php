<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for writing and deleting relationship tuples in OpenFGA.
 *
 * This request enables batch creation and deletion of relationship tuples,
 * allowing you to efficiently manage user-object relationships in a single
 * atomic operation. All changes are applied transactionally.
 *
 * @see WriteTuplesRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Tuples/Write Tuple write API endpoint
 */
final readonly class WriteTuplesRequest implements WriteTuplesRequestInterface
{
    /**
     * @param string                                     $store   The store ID
     * @param string                                     $model   Authorization model ID
     * @param TupleKeysInterface<TupleKeyInterface>|null $writes  Tuples to write (optional)
     * @param TupleKeysInterface<TupleKeyInterface>|null $deletes Tuples to delete (optional)
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $model,
        private ?TupleKeysInterface $writes = null,
        private ?TupleKeysInterface $deletes = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }
        if ('' === $this->model) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_MODEL_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDeletes(): ?TupleKeysInterface
    {
        return $this->deletes;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @inheritDoc
     *
     * @throws JsonException
     */
    #[Override]
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWrites(): ?TupleKeysInterface
    {
        return $this->writes;
    }
}
