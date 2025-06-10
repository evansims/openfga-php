<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use Override;

/**
 * Collection interface for OpenFGA type definition objects.
 *
 * This interface defines a collection that holds type definition objects
 * which specify the object types, their relations, and metadata within an
 * authorization model. Type definitions form the core schema that defines
 * how different object types relate to each other in the system.
 *
 * @extends IndexedCollectionInterface<\OpenFGA\Models\TypeDefinitionInterface>
 *
 * @see https://openfga.dev/docs/modeling/getting-started OpenFGA Authorization Models
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
    #[Override]
    public function jsonSerialize(): array;
}
