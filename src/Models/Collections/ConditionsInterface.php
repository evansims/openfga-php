<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ConditionInterface;
use Override;

/**
 * @template T of ConditionInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ConditionsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
