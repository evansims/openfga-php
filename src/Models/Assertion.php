<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

use function is_array;
use function is_string;

final class Assertion implements AssertionInterface
{
    public const OPENAPI_MODEL = 'Assertion';

    private static ?SchemaInterface $schema = null;

    /**
     * @param AssertionTupleKeyInterface                 $tupleKey         Tuple key for assertion.
     * @param bool                                       $expectation      Whether the assertion is expected to be true or false.
     * @param null|TupleKeysInterface<TupleKeyInterface> $contextualTuples Contextual tuples for assertion.
     * @param null|array<string, mixed>                  $context          Additional request context that will be used to evaluate any ABAC conditions encountered in the query evaluation.
     */
    public function __construct(
        private readonly AssertionTupleKeyInterface $tupleKey,
        private readonly bool $expectation,
        private readonly ?TupleKeysInterface $contextualTuples = null,
        private readonly ?array $context = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getContext(): ?array
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
    public function getExpectation(): bool
    {
        return $this->expectation;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleKey(): AssertionTupleKeyInterface
    {
        return $this->tupleKey;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        $data = [
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'expectation' => $this->expectation,
        ];

        if ($this->contextualTuples instanceof TupleKeysInterface) {
            $serialized = $this->contextualTuples->jsonSerialize();
            if (isset($serialized['tuple_keys']) && is_array($serialized['tuple_keys'])) {
                $data['contextual_tuples'] = $serialized['tuple_keys'];
            }
        }

        if (null !== $this->context) {
            $data['context'] = $this->context;
        }

        return $data;
    }

    /**
     * Create an Assertion from array data.
     *
     * @param array{
     *     tuple_key: AssertionTupleKeyInterface|array{user: string, relation: string, object: string},
     *     expectation: bool,
     *     contextual_tuples?: array<TupleKeyInterface|array{user: string, relation: string, object: string, condition?: array<string, mixed>|null}>|null,
     *     context?: array<string, mixed>|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $contextualTuples = null;

        if (isset($data['contextual_tuples']) && is_array($data['contextual_tuples'])) {
            $tupleKeys = new TupleKeys();

            // Handle wrapped format (with tuple_keys key) or direct array
            $tuplesArray = $data['contextual_tuples'];
            if (isset($tuplesArray['tuple_keys']) && is_array($tuplesArray['tuple_keys'])) {
                $tuplesArray = $tuplesArray['tuple_keys'];
            }

            foreach ($tuplesArray as $tupleArray) {
                // Handle both cases: already-transformed TupleKey objects or raw arrays
                if ($tupleArray instanceof TupleKeyInterface) {
                    $tupleKeys[] = $tupleArray;
                } elseif (is_array($tupleArray) && isset($tupleArray['user'], $tupleArray['relation'], $tupleArray['object'])) {
                    // Skip tuples with conditions since we don't have the Condition::fromArray method
                    if (isset($tupleArray['condition']) && is_array($tupleArray['condition'])) {
                        continue;
                    }

                    $user = $tupleArray['user'];
                    $relation = $tupleArray['relation'];
                    $object = $tupleArray['object'];

                    if (is_string($user) && is_string($relation) && is_string($object)) {
                        $tupleKeys[] = new TupleKey(
                            user: $user,
                            relation: $relation,
                            object: $object,
                            condition: null,
                        );
                    }
                }
            }
            $contextualTuples = $tupleKeys;
        }

        // Handle tuple_key - it might be an array that needs to be converted
        $tupleKey = $data['tuple_key'];
        if (is_array($tupleKey) && isset($tupleKey['user'], $tupleKey['relation'], $tupleKey['object'])) {
            $tupleKey = new AssertionTupleKey(
                user: $tupleKey['user'],
                relation: $tupleKey['relation'],
                object: $tupleKey['object'],
            );
        }

        if (! $tupleKey instanceof AssertionTupleKeyInterface) {
            throw new InvalidArgumentException('Invalid tuple_key provided to Assertion::fromArray');
        }

        return new self(
            tupleKey: $tupleKey,
            expectation: $data['expectation'],
            contextualTuples: $contextualTuples,
            context: $data['context'] ?? null,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tuple_key', type: 'object', className: AssertionTupleKey::class, required: true),
                new SchemaProperty(name: 'expectation', type: 'boolean', required: true),
                new SchemaProperty(name: 'contextual_tuples', type: 'array', items: ['type' => 'object', 'className' => TupleKey::class], required: false),
                new SchemaProperty(name: 'context', type: 'array', required: false),
            ],
        );
    }
}
