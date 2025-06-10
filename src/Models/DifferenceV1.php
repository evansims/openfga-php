<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a set difference operation between two usersets.
 *
 * In authorization models, you sometimes need to grant access to one group
 * of users while explicitly excluding another group. DifferenceV1 calculates
 * the difference between a base userset and a subtract userset, effectively
 * giving you "all users in base except those in subtract".
 *
 * For example, you might want to grant access to all employees except those
 * in a specific department, or all document viewers except the document owner.
 */
final class DifferenceV1 implements DifferenceV1Interface
{
    public const string OPENAPI_MODEL = 'v1.Difference';

    private static ?SchemaInterface $schema = null;

    /**
     * @param UsersetInterface $base     The base userset to calculate difference from
     * @param UsersetInterface $subtract The userset to subtract from base
     */
    public function __construct(
        private readonly UsersetInterface $base,
        private readonly UsersetInterface $subtract,
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
                new SchemaProperty(name: 'base', type: 'object', className: Userset::class, required: true),
                new SchemaProperty(name: 'subtract', type: 'object', className: Userset::class, required: true),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'base' => $this->base->jsonSerialize(),
            'subtract' => $this->subtract->jsonSerialize(),
        ];
    }
}
