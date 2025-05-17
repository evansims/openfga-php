<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @template T of AuthorizationModelInterface
 * @extends AbstractIndexedCollection<T>
 */
final class AuthorizationModels extends AbstractIndexedCollection implements AuthorizationModelsInterface
{
    /**
     * @var class-string<T>
     */
    protected static string $itemType = AuthorizationModel::class;

    /**
     * @param list<T>|T ...$models
     */
    public function __construct(iterable | AuthorizationModelInterface ...$models)
    {
        parent::__construct(...$models);
    }
}
