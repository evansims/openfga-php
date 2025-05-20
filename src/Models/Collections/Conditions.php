<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\ConditionInterface;

/**
 * @extends IndexedCollection<ConditionInterface>
 *
 * @implements ConditionsInterface<ConditionInterface>
 */
final class Conditions extends IndexedCollection implements ConditionsInterface
{
    protected static string $itemType = ConditionInterface::class;
}
