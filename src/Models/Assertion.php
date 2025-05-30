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
use function is_string;

final class Assertion implements AssertionInterface
{
    public const string OPENAPI_MODEL = 'Assertion';

    private static ?SchemaInterface $schema = null;

    /**
     * Create a new assertion to test authorization model correctness.
     *
     * @param AssertionTupleKeyInterface                 $tupleKey         The tuple key defining what authorization question to test (user, relation, object)
     * @param bool                                       $expectation      Whether the authorization check should return true (granted) or false (denied)
     * @param TupleKeysInterface<TupleKeyInterface>|null $contextualTuples Optional temporary tuples that exist only for this assertion evaluation
     * @param array<string, mixed>|null                  $context          Optional context data for evaluating ABAC conditions during the assertion
     */
    public function __construct(
        private readonly AssertionTupleKeyInterface $tupleKey,
        private readonly bool $expectation,
        private readonly ?TupleKeysInterface $contextualTuples = null,
        private readonly ?array $context = null,
    ) {
    }

    /**
     * Create an Assertion from array data.
     *
     * @param array{
     *     tuple_key: AssertionTupleKeyInterface|array{user: string, relation: string, object: string},
     *     expectation: bool,
     *     contextual_tuples?: array<TupleKeyInterface|array{user: string, relation: string, object: string, condition?: array<string, mixed>|null}>|null,
     *     context?: mixed
     * } $data
     *
     * @throws ClientThrowable          If the tuple_key is not a valid AssertionTupleKeyInterface
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public static function fromArray(array $data): self
    {
        $contextualTuples = null;

        if (isset($data['contextual_tuples'])) {
            $tupleKeys = new TupleKeys;

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

                    /** @var mixed $user */
                    $user = $tupleArray['user'];

                    /** @var mixed $relation */
                    $relation = $tupleArray['relation'];

                    /** @var mixed $object */
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
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_INVALID_TUPLE_KEY)]);
        }

        // Handle context - ensure it's an array or null
        /** @var mixed $contextData */
        $contextData = $data['context'] ?? null;
        $context = null;

        if (null !== $contextData) {
            if (is_string($contextData)) {
                // If context is a string, try to decode it as JSON
                /** @var mixed $decodedContext */
                $decodedContext = json_decode($contextData, true);
                $context = is_array($decodedContext) ? self::normalizeArrayKeys($decodedContext) : ['raw' => $contextData];
            } elseif (is_array($contextData)) {
                // Ensure array keys are strings
                $context = self::normalizeArrayKeys($contextData);
            } else {
                // If context is neither string, array, nor null, convert to array
                $context = ['value' => $contextData];
            }
        }

        return new self(
            tupleKey: $tupleKey,
            expectation: $data['expectation'],
            contextualTuples: $contextualTuples,
            context: $context,
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
     * Safely assign a mixed value to an array to satisfy Psalm.
     *
     * @param array<string, mixed> $array The target array
     * @param string               $key   The array key
     * @param mixed                $value The value to assign
     *
     * @psalm-suppress MixedAssignment
     */
    private static function assignMixed(array &$array, string $key, mixed $value): void
    {
        $array[$key] = $value;
    }

    /**
     * Normalize array keys to strings while preserving values.
     *
     * @param  array<array-key, mixed> $input
     * @return array<string, mixed>
     */
    private static function normalizeArrayKeys(array $input): array
    {
        /** @var array<string, mixed> $result */
        $result = [];

        /** @var mixed $value */
        foreach ($input as $key => $value) {
            // Convert array key to string and preserve mixed value from JSON API data
            self::assignMixed($result, (string) $key, $value);
        }

        return $result;
    }
}
