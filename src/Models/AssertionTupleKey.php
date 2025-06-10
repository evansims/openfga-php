<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a tuple key used for testing authorization model assertions.
 *
 * An AssertionTupleKey defines the specific user, relation, and object combination
 * that should be tested in authorization model assertions. This is used to verify
 * that your authorization model behaves correctly by testing whether specific
 * authorization questions return the expected results.
 *
 * Use this when creating test cases to validate your authorization rules
 * and ensure your permission model works as intended.
 */
final class AssertionTupleKey implements AssertionTupleKeyInterface
{
    public const string OPENAPI_MODEL = 'AssertionTupleKey';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $user     The user identifier for the assertion tuple key
     * @param string $relation The relation name for the assertion tuple key
     * @param string $object   The object identifier for the assertion tuple key
     */
    public function __construct(
        private readonly string $user,
        private readonly string $relation,
        private readonly string $object,
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
            ],
        );
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
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
        ];
    }
}
