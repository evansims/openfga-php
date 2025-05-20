<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\TypeDefinitionInterface;

/**
 * @template T of TypeDefinitionInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface TypeDefinitionsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     type: string,
     *     relations?: array<string, mixed>,
     *     metadata?: array<string, mixed>,
     * }>
     */
    public function jsonSerialize(): array;
}
