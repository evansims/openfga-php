<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * @extends IndexedCollectionInterface<\OpenFGA\Models\ConditionParameterInterface>
 */
interface ConditionParametersInterface extends IndexedCollectionInterface
{
    /**
     * @return list<array{type_name: string, generic_types?: array<int, mixed>}>
     */
    #[Override]
    public function jsonSerialize(): array;
}
