<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;
use InvalidArgumentException;

/**
 * Represents a reference to a specific relation on an object.
 *
 * In authorization models, you often need to reference relationships
 * between objects. An ObjectRelation identifies both the target object
 * and the specific relation you're interested in, like "the owner of document:budget"
 * or "editors of folder:reports".
 *
 * This is commonly used in authorization rules where permissions depend
 * on relationships with other objects in your system.
 */
final class ObjectRelation implements ObjectRelationInterface
{
    public const string OPENAPI_MODEL = 'ObjectRelation';

    private static ?SchemaInterface $schema = null;

    /**
     * @param ?string $object   The object identifier or null
     * @param string $relation The non-empty relation name
     */
    public function __construct(
        private readonly ?string $object = null,
        private readonly string $relation = '',
    ) {
        if ($this->relation === '') {
            throw new InvalidArgumentException('Relation cannot be empty.');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'object', type: 'string', required: false),
                new SchemaProperty(name: 'relation', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelation(): string
    {
        if ($this->relation === null || $this->relation === '') {
            // This case should ideally not be reached if constructor validation is correct
            throw new InvalidArgumentException('Relation cannot be null or empty.');
        }
        return $this->relation;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'object' => $this->object,
            'relation' => $this->relation,
        ], static fn ($value): bool => null !== $value && '' !== $value);
    }
}
