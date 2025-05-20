<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use OpenFGA\Models\Collections\{Computeds, ComputedsInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

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

    public function getComputed(): ComputedsInterface
    {
        return $this->computed;
    }

    public function getTupleset(): string
    {
        return $this->tupleset;
    }

    public function jsonSerialize(): array
    {
        return [
            'tupleset' => $this->tupleset,
            'computed' => $this->computed->jsonSerialize(),
        ];
    }

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
