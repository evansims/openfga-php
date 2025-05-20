<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\UsersetInterface;

/**
 * @template T of UsersetInterface
 *
 * @extends KeyedCollectionInterface<T>
 */
interface TypeDefinitionRelationsInterface extends KeyedCollectionInterface
{
    /**
     * Add a userset to the collection.
     *
     * @param string $key
     * @param T      $userset
     */
    public function add(string $key, UsersetInterface $userset): void;

    /**
     * Get the current userset in the collection.
     *
     * @return T
     */
    public function current(): UsersetInterface;

    /**
     * Serialize the collection to an array.
     *
     * @return array<int, array{
     *     computed_userset?: array{object?: string, relation?: string},
     *     tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}},
     *     union?: array<mixed>,
     *     intersection?: array<mixed>,
     *     difference?: array{base: array<mixed>, subtract: array<mixed>},
     *     direct?: object,
     * }>
     */
    public function jsonSerialize(): array;

    /**
     * Get a userset by offset.
     *
     * @param mixed $offset
     *
     * @return null|T
     */
    public function offsetGet(mixed $offset): ?UsersetInterface;
}
