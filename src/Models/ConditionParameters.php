<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, ConditionParameterInterface>
 * @implements \Iterator<int, ConditionParameterInterface>
 */
final class ConditionParameters extends AbstractIndexedCollection implements ConditionParametersInterface
{
    /**
     * @var class-string<ConditionParameterInterface>
     */
    protected static string $itemType = ConditionParameter::class;

    /**
     * @param ConditionParameterInterface|iterable<ConditionParameterInterface> ...$parameters
     */
    public function __construct(iterable | ConditionParameterInterface ...$parameters)
    {
        parent::__construct(...$parameters);
    }
}
