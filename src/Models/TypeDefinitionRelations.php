<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};

use function array_key_exists;
use function sprintf;

final class TypeDefinitionRelations implements TypeDefinitionRelationsInterface
{
    use KeyedCollectionTrait;

    private static ?CollectionSchemaInterface $schema = null;

    /**
     * @param array<string, UsersetInterface> $usersets
     */
    public function __construct(
        array $usersets = [],
    ) {
        $isAssoc = ! array_is_list($usersets);

        if ($isAssoc) {
            // For associative arrays, use the provided keys
            foreach ($usersets as $key => $userset) {
                $this->add($key, $userset);
            }
        } else {
            // For numeric arrays, use numeric indices as strings
            foreach ($usersets as $index => $userset) {
                $this->add((string) $index, $userset);
            }
        }
    }

    /**
     * Add a userset to the collection.

     *
     * @param string           $key
     * @param UsersetInterface $userset
     *
     * @throws InvalidArgumentException If the key is empty, already exists, or contains invalid characters
     */
    public function add(string $key, UsersetInterface $userset): void
    {
        $key = strtolower(trim($key));

        if ('' === $key) {
            throw new InvalidArgumentException('Key cannot be empty');
        }

        if (array_key_exists($key, $this->models)) {
            throw new InvalidArgumentException(sprintf('Key "%s" already exists in the collection', $key));
        }

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            throw new InvalidArgumentException('Key can only contain alphanumeric characters, underscores, and hyphens');
        }

        $this->models[$key] = $userset;
    }

    /**
     * Get the current userset in the collection.
     *
     * @return null|UsersetInterface
     */
    public function current(): ?UsersetInterface
    {
        $key = $this->key();

        return null === $key ? null : $this->models[$key];
    }

    /**
     * Get a userset by offset.
     *
     * @param mixed $offset
     *
     * @return null|UsersetInterface
     */
    public function offsetGet(mixed $offset): ?UsersetInterface
    {
        $key = strtolower(trim($offset));

        return $this->models[$key] ?? null;
    }

    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: self::class,
            itemType: Userset::class,
            requireItems: false,
        );
    }
}
