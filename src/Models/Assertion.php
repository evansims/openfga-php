<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Assertion implements AssertionInterface
{
    /**
     * @param AssertionTupleKeyInterface $tupleKey         Tuple key for assertion.
     * @param bool                       $expectation      Whether the assertion is expected to be true or false.
     * @param null|TupleKeysInterface    $contextualTuples Contextual tuples for assertion.
     * @param null|array                 $context          Additional request context that will be used to evaluate any ABAC conditions encountered in the query evaluation.
     */
    public function __construct(
        private AssertionTupleKeyInterface $tupleKey,
        private bool $expectation,
        private ?TupleKeysInterface $contextualTuples = null,
        private ?array $context = null,
    ) {
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

    public function getTupleKey(): AssertionTupleKeyInterface
    {
        return $this->tupleKey;
    }

    public function jsonSerialize(): array
    {
        return [
            'tuple_key' => $this->tupleKey->toArray(),
            'expectation' => $this->expectation,
            'contextual_tuples' => $this->contextualTuples?->toArray(),
            'context' => $this->context,
        ];
    }

    public static function fromArray(array $data): self
    {
        $tupleKey = $data['tuple_key'] ?? null;
        $expectation = $data['expectation'] ?? null;
        $contextualTuples = $data['contextual_tuples'] ?? null;
        $context = $data['context'] ?? null;

        $tupleKey = $tupleKey ? AssertionTupleKey::fromArray($tupleKey) : null;
        $expectation = $expectation ? (bool) $expectation : false;
        $contextualTuples = $contextualTuples ? TupleKeys::fromArray($contextualTuples) : null;
        $context = $context ? (array) $context : null;

        return new self(
            tupleKey: $tupleKey,
            expectation: $expectation,
            contextualTuples: $contextualTuples,
            context: $context,
        );
    }
}
