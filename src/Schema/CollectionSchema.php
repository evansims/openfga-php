<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use InvalidArgumentException;

use Override;

use function sprintf;

final class CollectionSchema implements CollectionSchemaInterface
{
    /**
     * @param class-string $className    The collection class name
     * @param class-string $itemType     The type of each item in the collection
     * @param bool         $requireItems Whether the collection requires at least one item
     *
     * @throws InvalidArgumentException If className or itemType are not valid, autoloadable classes
     */
    public function __construct(
        private readonly string $className,
        private readonly string $itemType,
        private readonly bool $requireItems = false,
    ) {
        if (! class_exists($this->className)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist or cannot be autoloaded', $this->className));
        }

        if (! class_exists($this->itemType)) {
            throw new InvalidArgumentException(sprintf('Item type "%s" does not exist or cannot be autoloaded', $this->itemType));
        }
    }

    #[Override]
    public function getClassName(): string
    {
        return $this->className;
    }

    #[Override]
    public function getItemType(): string
    {
        return $this->itemType;
    }

    #[Override]
    public function getProperties(): array
    {
        // Collection schemas don't have properties in the traditional sense
        return [];
    }

    #[Override]
    public function getProperty(string $name): ?SchemaProperty
    {
        // Collection schemas don't have properties in the traditional sense
        return null;
    }

    #[Override]
    public function requiresItems(): bool
    {
        return $this->requireItems;
    }
}
