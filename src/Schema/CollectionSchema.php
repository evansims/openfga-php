<?php

declare(strict_types=1);

namespace OpenFGA\Schema;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

/**
 * Schema definition specifically for validating and transforming collection data structures.
 *
 * This specialized schema handles arrays of objects, providing validation rules
 * for collections while ensuring each item conforms to the specified item type.
 * It supports wrapper keys for nested collection data and optional item requirements.
 *
 * @see CollectionSchemaInterface For the complete API specification
 */
final readonly class CollectionSchema implements CollectionSchemaInterface
{
    /**
     * Create a new collection schema definition.
     *
     * @param class-string $className    The fully qualified collection class name this schema defines
     * @param class-string $itemType     The fully qualified class name for individual items in the collection
     * @param bool         $requireItems Whether the collection must contain at least one item for validation to pass
     * @param string|null  $wrapperKey   Optional wrapper key for collections that expect data nested under a specific key (e.g., 'child' for Usersets)
     *
     * @throws ClientThrowable          If className or itemType are not valid, autoloadable classes
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $className,
        private string $itemType,
        private bool $requireItems = false,
        private ?string $wrapperKey = null,
    ) {
        if (! class_exists($this->className)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::SCHEMA_CLASS_NOT_FOUND, ['className' => $this->className])]);
        }

        if (! class_exists($this->itemType)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::SCHEMA_ITEM_TYPE_NOT_FOUND, ['itemType' => $this->itemType])]);
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getItemType(): string
    {
        return $this->itemType;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getProperties(): array
    {
        // Collection schemas don't have properties in the traditional sense
        return [];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getProperty(string $name): ?SchemaProperty
    {
        // Collection schemas don't have properties in the traditional sense
        return null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWrapperKey(): ?string
    {
        return $this->wrapperKey;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function requiresItems(): bool
    {
        return $this->requireItems;
    }
}
