<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{Computeds, ComputedsInterface};

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class UsersetTreeTupleToUserset implements UsersetTreeTupleToUsersetInterface
{
    public const OPENAPI_MODEL = 'UsersetTree.TupleToUserset';

    private static ?SchemaInterface $schema = null;

    /**
     * @param string                                $tupleset
     * @param ComputedsInterface<ComputedInterface> $computed
     */
    public function __construct(
        private readonly string $tupleset,
        private readonly ComputedsInterface $computed,
    ) {
    }

    #[Override]
    public function getComputed(): ComputedsInterface
    {
        return $this->computed;
    }

    #[Override]
    public function getTupleset(): string
    {
        return $this->tupleset;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset,
            'computed' => $this->computed->jsonSerialize(),
        ];
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'tupleset', type: 'string', required: true),
                new SchemaProperty(name: 'computed', type: Computeds::class, required: true),
            ],
        );
    }
}
