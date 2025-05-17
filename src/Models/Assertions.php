<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, AssertionInterface>
 * @implements \Iterator<int, AssertionInterface>
 */
final class Assertions extends AbstractIndexedCollection implements AssertionsInterface
{
    /**
     * @var class-string<AssertionInterface>
     */
    protected static string $itemType = Assertion::class;

    /**
     * @param AssertionInterface|iterable<AssertionInterface> ...$assertions
     */
    public function __construct(iterable | AssertionInterface ...$assertions)
    {
        parent::__construct(...$assertions);
    }
}
