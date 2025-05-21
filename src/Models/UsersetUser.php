<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class UsersetUser implements UsersetUserInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly string $type,
        private readonly string $id,
        private readonly string $relation,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'relation' => $this->relation,
        ];
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'type', type: 'string', required: true),
                new SchemaProperty(name: 'id', type: 'string', required: true),
                new SchemaProperty(name: 'relation', type: 'string', required: true),
            ],
        );
    }
}
