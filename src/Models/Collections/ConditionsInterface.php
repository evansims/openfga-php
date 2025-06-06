<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * Collection interface for OpenFGA condition objects.
 *
 * This interface defines a collection that holds condition objects used in
 * authorization models to implement context-aware authorization decisions.
 * Conditions allow for dynamic authorization based on attributes and runtime
 * context, enabling sophisticated access control patterns.
 *
 * Each condition includes an expression, parameters, and optional metadata
 * that define how the condition should be evaluated during authorization checks.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\ConditionInterface>
 *
 * @see https://openfga.dev/docs/modeling/conditions OpenFGA Conditions
 */
interface ConditionsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
