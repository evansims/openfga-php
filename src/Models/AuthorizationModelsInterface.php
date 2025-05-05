<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelsInterface extends ModelCollectionInterface
{
    /**
     * Add an authorization model to the collection.
     *
     * @param AuthorizationModelInterface $authorizationModel
     */
    public function add(AuthorizationModelInterface $authorizationModel): void;

    /**
     * Get the current authorization model in the collection.
     *
     * @return AuthorizationModelInterface
     */
    public function current(): AuthorizationModelInterface;

    /**
     * Get an authorization model by offset.
     *
     * @param mixed $offset
     *
     * @return null|AuthorizationModelInterface
     */
    public function offsetGet(mixed $offset): ?AuthorizationModelInterface;
}
