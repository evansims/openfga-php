<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, ConditionInterface>
 * @implements \Iterator<int, ConditionInterface>
 */
final class Conditions extends AbstractIndexedCollection implements ConditionsInterface
{
    /**
     * @var class-string<ConditionInterface>
     */
    protected static string $itemType = Condition::class;

    /**
     * @param ConditionInterface|iterable<ConditionInterface> ...$conditions
     */
    public function __construct(iterable | ConditionInterface ...$conditions)
    {
        parent::__construct(...$conditions);
    }
}
