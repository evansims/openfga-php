<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<Store>
 */
final class Stores extends AbstractIndexedCollection implements StoresInterface
{
    protected static string $itemType = Store::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @return null|StoreInterface
     */
    public function current(): ?StoreInterface
    {
        /** @var null|StoreInterface $result */
        return parent::current();
    }

    /**
     * @param mixed $offset
     *
     * @return null|StoreInterface
     */
    public function offsetGet(mixed $offset): ?StoreInterface
    {
        /** @var null|StoreInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof StoreInterface ? $result : null;
    }
}
