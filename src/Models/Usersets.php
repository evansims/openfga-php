<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\CollectionSchemaInterface;

/**
 * @extends AbstractIndexedCollection<Userset>
 *
 * @implements UsersetsInterface<Userset>
 */
final class Usersets extends AbstractIndexedCollection implements UsersetsInterface
{
    protected static string $itemType = Userset::class;

    protected static ?CollectionSchemaInterface $schema = null;

    /**
     * @param ModelInterface $userset Must be an instance of Userset
     */
    public function add(ModelInterface $userset): void
    {
        if (! $userset instanceof Userset) {
            throw new InvalidArgumentException('Expected instance of ' . Userset::class . ', got ' . get_debug_type($userset));
        }
        parent::add($userset);
    }
}
