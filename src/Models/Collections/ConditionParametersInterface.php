<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ConditionParameterInterface;

/**
 * @template T of ConditionParameterInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface ConditionParametersInterface extends IndexedCollectionInterface
{
    /**
     * @return list<array{type_name: string, generic_types?: array<int, mixed>}>
     */
    public function jsonSerialize(): array;
}
