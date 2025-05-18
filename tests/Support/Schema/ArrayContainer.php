<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Support\Schema;

final class ArrayContainer
{
    /**
     * @var ArrayItem[]
     */
    private array $items = [];

    /**
     * @param ArrayItem[] $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @return ArrayItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param ArrayItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
