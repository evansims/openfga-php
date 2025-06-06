<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
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
 * Request for performing authorization checks in OpenFGA.
 *
 * This request determines whether a user has a specific relationship with an object
 * based on the configured authorization model and relationship tuples. It's the core
 * operation for making authorization decisions in your application.
 *
 * The check operation supports contextual tuples, custom contexts, and tracing to
 * provide comprehensive authorization decisions with detailed debugging information.
 *
 * @see CheckRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/Check Authorization check API endpoint
 */
final readonly class CheckRequest implements CheckRequestInterface
{
    /**
     * @param string              $store
     * @param string              $model
     * @param TupleKeyInterface   $tupleKey
     * @param ?bool               $trace
     * @param ?object             $context
     * @param ?TupleKeysInterface $contextualTuples
     * @param ?Consistency        $consistency
     *
     * @throws ClientThrowable          If the store ID or model ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private string $model,
        private TupleKeyInterface $tupleKey,
        private ?bool $trace = null,
        private ?object $context = null,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?Consistency $consistency = null,
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
    public function getAuthorizationModel(): string
    {
        return $this->model;
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
    public function getContext(): ?object
    {
        return $this->context;
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
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $contextualTuples = $this->contextualTuples?->jsonSerialize();

        $body = array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'authorization_model_id' => $this->model,
            'trace' => $this->trace,
            'context' => $this->context,
            'consistency' => $this->consistency?->value,
            'contextual_tuples' => $contextualTuples,
        ], static fn ($value): bool => null !== $value && (! is_array($value) || [] !== $value));

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/check',
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
    public function getTrace(): ?bool
    {
        return $this->trace;
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
