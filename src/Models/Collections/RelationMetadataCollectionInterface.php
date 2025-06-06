<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

/**
 * Collection interface for OpenFGA relation metadata objects.
 *
 * This interface defines a keyed collection that holds relation metadata objects,
 * which provide additional information about the relations defined in authorization
 * model type definitions. Relation metadata includes details such as the module
 * name and source file information for authorization models.
 *
 * The collection is keyed by relation names, allowing efficient access to metadata
 * for specific relations within a type definition.
 *
 * @extends KeyedCollectionInterface<\OpenFGA\Models\RelationMetadataInterface>
 *
 * @see https://openfga.dev/docs/modeling/getting-started OpenFGA Authorization Models
 */
interface RelationMetadataCollectionInterface extends KeyedCollectionInterface
{
}
