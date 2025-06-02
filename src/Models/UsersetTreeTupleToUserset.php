<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

/**
 * Represents a tuple-to-userset operation node in authorization evaluation trees.
 *
 * UsersetTreeTupleToUserset defines how to resolve users through tuple-to-userset
 * mappings during authorization evaluation. It specifies which tuples to examine
 * and how to compute the resulting usersets, enabling complex authorization
 * patterns based on indirect relationships.
 *
 * Use this when working with authorization evaluation trees that involve
 * tuple-to-userset relationship resolution.
 */
final class UsersetTreeTupleToUserset implements UsersetTreeTupleToUsersetInterface
{
    public const string OPENAPI_MODEL = 'UsersetTree.TupleToUserset';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                        $tupleset
     * @param array<int, ComputedInterface> $computed
     */
    public function __construct(
        private readonly string $tupleset,
        private readonly array $computed,
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
                new SchemaProperty(name: 'tupleset', type: 'string', required: true),
                new SchemaProperty(name: 'computed', type: 'array', required: true, items: ['type' => 'object', 'className' => Computed::class]),
            ],
        );
    }

    /**
     * @inheritDoc
     *
     * @return array<int, ComputedInterface>
     */
    #[Override]
    public function getComputed(): array
    {
        return $this->computed;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleset(): string
    {
        return $this->tupleset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset,
            'computed' => array_map(/**
             * @return string[]
             *
             * @psalm-return array{userset: string}
             */
                static fn (ComputedInterface $c): array => $c->jsonSerialize(),
                $this->computed,
            ),
        ];
    }
}
