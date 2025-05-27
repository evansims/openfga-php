<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ConditionParameter, ConditionParameterInterface};

/**
 * @extends IndexedCollection<ConditionParameterInterface>
 *
 * @implements ConditionParametersInterface<ConditionParameterInterface>
 */
final class ConditionParameters extends IndexedCollection implements ConditionParametersInterface
{
    protected static string $itemType = ConditionParameter::class;
}
