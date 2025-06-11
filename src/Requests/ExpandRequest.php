<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

use function is_array;

/**
 * Request for expanding a relationship to show all users who have that relationship.
 *
 * This request returns the complete set of users and usersets that have the specified
 * relationship with an object. It's useful for debugging authorization models, auditing
 * permissions, and understanding the complete authorization tree.
 *
 * @see ExpandRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/Expand Expand API endpoint
 */
final readonly class ExpandRequest implements ExpandRequestInterface
{
    /**
     * @param string              $store            The store ID
     * @param TupleKeyInterface   $tupleKey         The tuple key to expand
     * @param ?string             $model            Authorization model ID (optional)
     * @param ?TupleKeysInterface $contextualTuples Contextual tuples (optional)
     * @param ?Consistency        $consistency      Consistency requirement (optional)
     *
     * @throws ClientThrowable          If the store ID is empty or model ID is empty (when provided)
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private TupleKeyInterface $tupleKey,
        private ?string $model = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getConsistency(): ?Consistency
    {
        return $this->consistency;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getModel(): ?string
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
        $contextualTuples = $this->contextualTuples?->jsonSerialize();

        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->model,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $contextualTuples,
        ], static fn ($value): bool => null !== $value && (! is_array($value) || [] !== $value));

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/expand',
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
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }
}
