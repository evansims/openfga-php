<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Models\{TupleKey, TupleKeys, TupleKeyInterface, TupleKeysInterface};

final class Assertion implements AssertionInterface
{
    /**
     * @param TupleKeyInterface       $tupleKey         Tuple key for assertion.
     * @param bool                    $expectation      Whether the assertion is expected to be true or false.
     * @param null|TupleKeysInterface $contextualTuples Contextual tuples for assertion.
     * @param null|array              $context          Additional request context that will be used to evaluate any ABAC conditions encountered in the query evaluation.
     */
    public function __construct(
        private TupleKeyInterface $tupleKey,
        private bool $expectation,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?array $context = null,
    ) {
    }

    public static function Schema(): SchemaInterface
    {
        return new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tuple_key', type: TupleKey::class, required: true),
                new SchemaProperty(name: 'expectation', type: 'boolean', required: true),
                new SchemaProperty(name: 'contextual_tuples', type: TupleKeys::class, required: false),
                new SchemaProperty(name: 'context', type: 'array', required: false),
            ],
        );
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function getContextualTuples(): ?TupleKeysInterface
    {
        return $this->contextualTuples;
    }

    public function getExpectation(): bool
    {
        return $this->expectation;
    }

    public function getTupleKey(): TupleKeyInterface
    {
        return $this->tupleKey;
    }

    public function jsonSerialize(): array
    {
        $response = [];

        $response['tuple_key'] = $this->getTupleKey()->jsonSerialize();
        $response['expectation'] = $this->getExpectation();

        if (null !== $this->getContextualTuples()) {
            $response['contextual_tuples'] = $this->getContextualTuples()->jsonSerialize();
        }

        if (null !== $this->getContext()) {
            $response['context'] = $this->getContext();
        }

        return $response;
    }
}
