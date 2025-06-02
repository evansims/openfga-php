<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a computed userset reference in authorization evaluation trees.
 *
 * A Computed defines a userset that is calculated based on relationships
 * or other dynamic criteria rather than being explicitly defined. This is
 * used in authorization evaluation trees to represent usersets that are
 * derived through computation during the authorization check process.
 *
 * Use this when working with complex authorization patterns that involve
 * computed or derived user groups.
 */
final class Computed implements ComputedInterface
{
    public const string OPENAPI_MODEL = 'Computed';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string $userset The userset specification for the computed relation
     */
    public function __construct(
        private readonly string $userset,
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
                new SchemaProperty(name: 'userset', type: 'string', required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUserset(): string
    {
        return $this->userset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'userset' => $this->userset,
        ];
    }
}
