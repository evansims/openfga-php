<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of ConditionParameterInterface
 * @extends AbstractIndexedCollection<T>
 */
final class ConditionParameters extends AbstractIndexedCollection implements ConditionParametersInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = ConditionParameter::class;

    /**
     * @param list<T>|T ...$parameters
     */
    public function __construct(iterable | ConditionParameterInterface ...$parameters)
    {
        parent::__construct(...$parameters);
    }
}
