<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{ConditionParameter, ConditionParameterInterface, ModelInterface};

/**
 * @extends IndexedCollection<ConditionParameterInterface>
 *
 * @implements ConditionParametersInterface<ConditionParameterInterface>
 */
final class ConditionParameters extends IndexedCollection implements ConditionParametersInterface
{
    /**
     * @phpstan-var class-string<ConditionParameterInterface>
     *
     * @psalm-var class-string<ModelInterface>
     *
     * @var class-string<ModelInterface>
     */
    protected static string $itemType = ConditionParameter::class;
}
