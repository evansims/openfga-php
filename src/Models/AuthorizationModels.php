<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<AuthorizationModel>
 */
final class AuthorizationModels extends AbstractIndexedCollection implements AuthorizationModelsInterface
{
    protected static string $itemType = AuthorizationModel::class;
}
