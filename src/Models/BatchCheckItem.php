<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

use function is_array;
use function is_object;
use function is_string;
use function preg_match;

/**
 * Represents a single item in a batch check request.
 *
 * Each batch check item contains a tuple key to check, an optional context,
 * optional contextual tuples, and a correlation ID to map the result back
 * to this specific check.
 *
 * @see BatchCheckItemInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/BatchCheck
 */
final class BatchCheckItem implements BatchCheckItemInterface
{
    public const string OPENAPI_MODEL = 'BatchCheckItem';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new batch check item.
     *
     * @param TupleKeyInterface                      $tupleKey         The tuple key to check
     * @param string                                 $correlationId    Unique identifier for this check (max 36 chars, alphanumeric + hyphens)
     * @param ?TupleKeysInterface<TupleKeyInterface> $contextualTuples Optional contextual tuples for this check
     * @param ?object                                $context          Optional context object for this check
     *
     * @throws ClientThrowable          If the correlation ID is invalid
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private readonly TupleKeyInterface $tupleKey,
        private readonly string $correlationId,
        private readonly ?TupleKeysInterface $contextualTuples = null,
        private readonly ?object $context = null,
    ) {
        // Validate correlation ID format
        if (1 !== preg_match('/^[\w\d-]{1,36}$/', $this->correlationId)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::INVALID_CORRELATION_ID, ['correlationId' => $this->correlationId, 'pattern' => '^[\w\d-]{1,36}$', ], ), ]);
        }
    }

    /**
     * @inheritDoc
     *
     * @param array<string, mixed> $data
     *
     * @throws ClientThrowable          If the batch check item creation fails
     * @throws InvalidArgumentException If the provided data structure is invalid
     * @throws ReflectionException      If schema reflection fails
     */
    public static function fromArray(array $data): static
    {
        // Create TupleKey manually since it doesn't have fromArray
        if (! isset($data['tuple_key']) || ! is_array($data['tuple_key'])) {
            throw new InvalidArgumentException('Missing or invalid tuple_key data');
        }

        $tupleKeyData = $data['tuple_key'];
        if (! is_string($tupleKeyData['user'] ?? null)
            || ! is_string($tupleKeyData['relation'] ?? null)
            || ! is_string($tupleKeyData['object'] ?? null)) {
            throw new InvalidArgumentException('Invalid tuple key data structure');
        }

        $condition = null;
        if (isset($tupleKeyData['condition']) && $tupleKeyData['condition'] instanceof ConditionInterface) {
            $condition = $tupleKeyData['condition'];
        }

        /** @var string $user */
        $user = $tupleKeyData['user'] ?? throw new InvalidArgumentException('Missing user in tuple key data');

        /** @var string $relation */
        $relation = $tupleKeyData['relation'] ?? throw new InvalidArgumentException('Missing relation in tuple key data');

        /** @var string $object */
        $object = $tupleKeyData['object'] ?? throw new InvalidArgumentException('Missing object in tuple key data');

        $tupleKey = new TupleKey(
            user: $user,
            relation: $relation,
            object: $object,
            condition: $condition,
        );

        if (! isset($data['correlation_id']) || ! is_string($data['correlation_id'])) {
            throw new InvalidArgumentException('Missing or invalid correlation_id');
        }

        $correlationId = $data['correlation_id'];

        $contextualTuples = null;
        if (isset($data['contextual_tuples']) && is_array($data['contextual_tuples'])) {
            $tuples = new TupleKeys;

            // Handle both serialized format (with 'tuple_keys' wrapper) and direct array format
            $tuplesArray = $data['contextual_tuples'];
            if (isset($tuplesArray['tuple_keys']) && is_array($tuplesArray['tuple_keys'])) {
                $tuplesArray = $tuplesArray['tuple_keys'];
            }

            /** @var mixed $tupleArray */
            foreach ($tuplesArray as $tupleArray) {
                if (is_array($tupleArray)
                    && is_string($tupleArray['user'] ?? null)
                    && is_string($tupleArray['relation'] ?? null)
                    && is_string($tupleArray['object'] ?? null)) {
                    /** @var array<string, mixed> $tupleArray */
                    $tupleCondition = null;
                    if (isset($tupleArray['condition']) && $tupleArray['condition'] instanceof ConditionInterface) {
                        $tupleCondition = $tupleArray['condition'];
                    }

                    /** @var string $tupleUser */
                    $tupleUser = $tupleArray['user'] ?? '';

                    /** @var string $tupleRelation */
                    $tupleRelation = $tupleArray['relation'] ?? '';

                    /** @var string $tupleObject */
                    $tupleObject = $tupleArray['object'] ?? '';

                    $tuples->add(new TupleKey(
                        user: $tupleUser,
                        relation: $tupleRelation,
                        object: $tupleObject,
                        condition: $tupleCondition,
                    ));
                }
            }
            $contextualTuples = $tuples;
        }

        $context = null;
        if (isset($data['context']) && is_object($data['context'])) {
            $context = $data['context'];
        }

        return new self(
            tupleKey: $tupleKey,
            correlationId: $correlationId,
            contextualTuples: $contextualTuples,
            context: $context,
        );
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException If schema reflection fails
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tuple_key', type: 'object', required: true),
                new SchemaProperty(name: 'correlation_id', type: 'string', required: true),
                new SchemaProperty(name: 'contextual_tuples', type: 'array', required: false),
                new SchemaProperty(name: 'context', type: 'object', required: false),
            ],
        );
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
    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'correlation_id' => $this->correlationId,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
            'context' => $this->context,
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'correlation_id' => $this->correlationId,
        ];

        if ($this->contextualTuples instanceof TupleKeysInterface) {
            $data['contextual_tuples'] = $this->contextualTuples->toArray();
        }

        if (null !== $this->context) {
            $data['context'] = $this->context;
        }

        return $data;
    }
}
