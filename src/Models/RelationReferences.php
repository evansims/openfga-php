<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{CollectionSchema, CollectionSchemaInterface};

use function array_key_exists;
use function sprintf;

final class RelationReferences implements RelationReferencesInterface
{
    use KeyedCollectionTrait;

    private static ?CollectionSchemaInterface $schema = null;

    /**
     * @param array<string, RelationReferenceInterface> $relationReferences
     */
    public function __construct(
        array $relationReferences = [],
    ) {
        $isAssoc = ! array_is_list($relationReferences);

        if ($isAssoc) {
            // For associative arrays, use the provided keys
            foreach ($relationReferences as $key => $relationReference) {
                $this->add($key, $relationReference);
            }
        } else {
            // For numeric arrays, use numeric indices as strings
            foreach ($relationReferences as $index => $relationReference) {
                $this->add((string) $index, $relationReference);
            }
        }
    }

    /**
     * Add a relation reference to the collection.

     *
     * @param string                     $key
     * @param RelationReferenceInterface $relationReference
     *
     * @throws InvalidArgumentException If the key is empty, already exists, or contains invalid characters
     */
    public function add(string $key, RelationReferenceInterface $relationReference): void
    {
        if ('' === trim($key)) {
            throw new InvalidArgumentException('Key cannot be empty');
        }

        if (array_key_exists($key, $this->models)) {
            throw new InvalidArgumentException(sprintf('Key "%s" already exists in the collection', $key));
        }

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
            throw new InvalidArgumentException('Key can only contain alphanumeric characters, underscores, and hyphens');
        }

        $this->models[$key] = $relationReference;
    }

    public function current(): ?RelationReferenceInterface
    {
        $key = $this->key();

        return null === $key ? null : $this->models[$key];
    }

    public function offsetGet(mixed $offset): ?RelationReferenceInterface
    {
        $key = $offset;

        return null === $key ? null : $this->models[$key];
    }

    public static function schema(): CollectionSchemaInterface
    {
        return self::$schema ??= new CollectionSchema(
            className: self::class,
            itemType: RelationReference::class,
            requireItems: false,
        );
    }
}
