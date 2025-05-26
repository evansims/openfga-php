<?php

declare(strict_types=1);

namespace OpenFGA\Models\Collections;

use OpenFGA\Models\{AuthorizationModelInterface, AuthorizationModel};

/**
 * @extends IndexedCollection<AuthorizationModelInterface>
 *
 * @implements AuthorizationModelsInterface<AuthorizationModelInterface>
 */
final class AuthorizationModels extends IndexedCollection implements AuthorizationModelsInterface
{
    protected static string $itemType = AuthorizationModel::class;
}
