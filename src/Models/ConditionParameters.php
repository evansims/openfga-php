<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<ConditionParameter>
 */
final class ConditionParameters extends AbstractIndexedCollection implements ConditionParametersInterface
{
    protected static string $itemType = ConditionParameter::class;
}
