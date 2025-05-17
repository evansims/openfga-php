<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @implements \ArrayAccess<int, AuthorizationModelInterface>
 * @implements \Iterator<int, AuthorizationModelInterface>
 */
final class AuthorizationModels extends AbstractIndexedCollection implements AuthorizationModelsInterface
{
    /**
     * @var class-string<AuthorizationModelInterface>
     */
    protected static string $itemType = AuthorizationModel::class;

    /**
     * @param AuthorizationModelInterface|iterable<AuthorizationModelInterface> ...$models
     */
    public function __construct(iterable | AuthorizationModelInterface ...$models)
    {
        parent::__construct(...$models);
    }
}
