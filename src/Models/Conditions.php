<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<Condition>
 */
final class Conditions extends AbstractIndexedCollection implements ConditionsInterface
{
    protected static string $itemType = Condition::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|ConditionInterface
     */
    public function current(): ?ConditionInterface
    {
        /** @var null|ConditionInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|ConditionInterface
     */
    public function offsetGet(mixed $offset): ?ConditionInterface
    {
        /** @var null|ConditionInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof ConditionInterface ? $result : null;
    }
}
