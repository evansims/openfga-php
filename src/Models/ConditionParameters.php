<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<ConditionParameter>
 */
final class ConditionParameters extends AbstractIndexedCollection implements ConditionParametersInterface
{
    protected static string $itemType = ConditionParameter::class;

    /**
     * @return null|ConditionParameterInterface
     */
    public function current(): ?ConditionParameterInterface
    {
        /** @var null|ConditionParameterInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|ConditionParameterInterface
     */
    public function offsetGet(mixed $offset): ?ConditionParameterInterface
    {
        /** @var null|ConditionParameterInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof ConditionParameterInterface ? $result : null;
    }
}
