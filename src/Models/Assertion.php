<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

use function is_array;

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

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getExpectation(): bool
    {
        return $this->expectation;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getTupleKey(): AssertionTupleKeyInterface
    {
        return $this->tupleKey;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'tuple_key' => $this->tupleKey->jsonSerialize(),
            'expectation' => $this->expectation,
            'contextual_tuples' => $this->contextualTuples?->jsonSerialize(),
            'context' => $this->context,
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tuple_key', type: 'object', className: AssertionTupleKey::class, required: true),
                new SchemaProperty(name: 'expectation', type: 'bool', required: true),
                new SchemaProperty(name: 'contextual_tuples', type: 'object', className: TupleKeys::class, required: false),
                new SchemaProperty(name: 'context', type: 'array', required: false),
            ],
        );
    }
}
