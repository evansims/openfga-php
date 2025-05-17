<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of AssertionInterface
 * @extends AbstractIndexedCollection<T>
 */
final class Assertions extends AbstractIndexedCollection implements AssertionsInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = Assertion::class;

    /**
     * @param list<T>|T ...$assertions
     */
    public function __construct(iterable | AssertionInterface ...$assertions)
    {
        parent::__construct(...$assertions);
    }
}
