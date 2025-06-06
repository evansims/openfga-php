<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\TupleKeysInterface;
use Override;

/**
 * Represents an assertion used to test authorization model correctness.
 *
 * Assertions are test cases that verify whether specific authorization
 * decisions should be allowed or denied. They are essential for validating
 * authorization models and ensuring they behave as expected.
 *
 * Each assertion includes a tuple key to test, the expected result,
 * and optional contextual information for complex scenarios.
 */
interface AssertionInterface extends ModelInterface
{
    /**
     * Get the context data for evaluating ABAC conditions.
     *
     * Context provides additional information that can be used when
     * evaluating attribute-based access control (ABAC) conditions.
     * This might include user attributes, resource properties,
     * or environmental factors like time of day.
     *
     * @return ?array<string, mixed> The context data for condition evaluation, or null if not needed
     */
    public function getContext(): ?array;

    /**
     * Get the contextual tuples for this assertion.
     *
     * Contextual tuples provide additional relationship data that should
     * be considered when evaluating the assertion. These are temporary
     * relationships that exist only for the duration of the authorization check,
     * useful for testing "what-if" scenarios.
     *
     * @return ?TupleKeysInterface The contextual tuples, or null if not needed
     */
    public function getContextualTuples(): ?TupleKeysInterface;

    /**
     * Get the expected result for this assertion.
     *
     * The expectation defines whether the authorization check should
     * return true (access granted) or false (access denied).
     * This is what the assertion will be tested against.
     *
     * @return bool True if access should be granted, false if access should be denied
     */
    public function getExpectation(): bool;

    /**
     * Get the tuple key that defines what to test.
     *
     * The tuple key specifies the exact authorization question to ask:
     * "Does user X have relation Y on object Z?"
     * This is the core of what the assertion is testing.
     *
     * @return AssertionTupleKeyInterface The tuple key defining the authorization question
     */
    public function getTupleKey(): AssertionTupleKeyInterface;

    /**
     * @return array{
     *     tuple_key: array{user: string, relation: string, object: string},
     *     expectation: bool,
     *     contextual_tuples?: array<array-key, mixed>,
     *     context?: array<array-key, mixed>
     * }
     */
    #[Override]
    public function jsonSerialize(): array;
}
