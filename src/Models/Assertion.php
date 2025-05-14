<?php

declare(strict_types=1);

namespace OpenFGA\Models;

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

    public static function fromArray(array $data): self
    {
        $data = self::validatedAssertionShape($data);

        return new self(
            tupleKey: TupleKey::fromArray(TupleKeyType::ASSERTION_TUPLE_KEY, $data['tuple_key']),
            expectation: $data['expectation'],
            contextualTuples: isset($data['contextual_tuples']) ? TupleKeys::fromArray(TupleKeyType::ASSERTION_TUPLE_KEY, $data['contextual_tuples']) : null,
            context: isset($data['context']) ? (array) $data['context'] : null,
        );
    }

    /**
     * Validates the shape of the array to be used as assertion data. Throws an exception if the data is invalid.
     *
     * @param array{tuple_key: TupleKeyShape, expectation: bool, contextual_tuples?: TupleKeysShape, context?: array} $data
     *
     * @throws InvalidArgumentException
     *
     * @return AssertionShape
     */
    public static function validatedAssertionShape(array $data): array
    {
        return $data;
    }
}
