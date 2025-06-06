<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientException};
use OpenFGA\Messages;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

/**
 * Represents a relationship tuple key defining a connection between user, relation, and object.
 *
 * A TupleKey is the fundamental unit of authorization in OpenFGA, representing
 * a specific relationship like "user:anne is reader of document:budget".
 * It consists of three parts: user (who), relation (what type of access),
 * and object (what resource), optionally with conditions for attribute-based access.
 *
 * Use this when creating, querying, or managing specific relationships in your
 * authorization system.
 */
final class TupleKey implements TupleKeyInterface
{
    public const string OPENAPI_MODEL = 'TupleKey';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                  $user      The user associated with the tuple key
     * @param string                  $relation  The relation associated with the tuple key
     * @param string                  $object    The object associated with the tuple key
     * @param ConditionInterface|null $condition Optional condition for the tuple key
     *
     * @throws ClientException          If the user or object identifier format is invalid
     * @throws InvalidArgumentException If translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private readonly string $user,
        private readonly string $relation,
        private readonly string $object,
        private readonly ?ConditionInterface $condition = null,
    ) {
        // Validate that identifiers don't contain internal whitespace
        // We check for whitespace between non-whitespace characters, not leading/trailing
        if (1 === preg_match('/\S\s+\S/', $this->user)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_INVALID_IDENTIFIER_FORMAT, ['identifier' => $this->user, ])]);
        }

        if (1 === preg_match('/\S\s+\S/', $this->object)) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_INVALID_IDENTIFIER_FORMAT, ['identifier' => $this->object, ])]);
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
