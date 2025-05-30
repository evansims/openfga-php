<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class TupleKey implements TupleKeyInterface
{
    public const string OPENAPI_MODEL = 'TupleKey';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                  $user      The user associated with the tuple key
     * @param string                  $relation  The relation associated with the tuple key
     * @param string                  $object    The object associated with the tuple key
     * @param ConditionInterface|null $condition Optional condition for the tuple key
     */
    public function __construct(
        private readonly string $user,
        private readonly string $relation,
        private readonly string $object,
        private readonly ?ConditionInterface $condition = null,
    ) {
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
                new SchemaProperty(name: 'user', type: 'string', required: true),
                new SchemaProperty(name: 'relation', type: 'string', required: true),
                new SchemaProperty(name: 'object', type: 'string', required: true),
                new SchemaProperty(name: 'condition', type: 'object', className: Condition::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getCondition(): ?ConditionInterface
    {
        return $this->condition;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
            'condition' => $this->condition?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }
}
