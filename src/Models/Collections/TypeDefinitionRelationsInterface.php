<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;
use Override;

/**
 * @template T of UsersetInterface
 *
 * @extends KeyedCollectionInterface<T>
 */
interface TypeDefinitionRelationsInterface extends KeyedCollectionInterface
{
    /**
     * Serialize the collection to an array.
     *
     * @return array<string, array{
     *     computed_userset?: array{object?: string, relation?: string},
     *     tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     direct?: object,
     * }>
     */
    #[Override]
    public function jsonSerialize(): array;
}
