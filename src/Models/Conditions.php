<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of ConditionInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Conditions extends AbstractIndexedCollection implements ConditionsInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = Condition::class;

    /**
     * @param list<T>|T ...$conditions
     */
    public function __construct(iterable | ConditionInterface ...$conditions)
    {
        parent::__construct(...$conditions);
    }
}
