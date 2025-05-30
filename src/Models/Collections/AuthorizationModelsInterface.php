<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\AuthorizationModelInterface;
use Override;

/**
 * Collection interface for OpenFGA authorization model objects.
 *
 * This interface defines a collection that holds authorization model objects,
 * which define the relationship structure and permissions within an OpenFGA
 * store. Each model contains type definitions, relations, and optionally conditions.
 *
 * @template T of AuthorizationModelInterface
 *
 * @extends IndexedCollectionInterface<T>
 *
 * @see https://openfga.dev/docs/concepts#authorization-models OpenFGA Authorization Models
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
    #[Override]
    public function jsonSerialize(): array;
}
