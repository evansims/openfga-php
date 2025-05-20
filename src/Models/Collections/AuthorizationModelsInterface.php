<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AuthorizationModelInterface;

/**
 * @template T of AuthorizationModelInterface
 *
 * @extends IndexedCollectionInterface<T>
 */
interface AuthorizationModelsInterface extends IndexedCollectionInterface
{
    /**
     * @return array<int, array{
     *     id: string,
     *     schema_version: string,
     *     type_definitions: array<int, array<int, array{type: string, relations?: array<string, mixed>, metadata?: array<string, mixed>}>>,
     *     conditions?: array<int, array<int, array{name: string, expression: string, parameters?: array<string, mixed>, metadata?: array<string, mixed>}>>,
     * }>
     */
    public function jsonSerialize(): array;
}
