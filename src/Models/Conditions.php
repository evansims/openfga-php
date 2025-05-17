<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<Condition>
 */
final class Conditions extends AbstractIndexedCollection implements ConditionsInterface
{
    protected static string $itemType = Condition::class;
}
