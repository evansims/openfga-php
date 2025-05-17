<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type AuthorizationModelsShape = list<AuthorizationModelShape>
 */
interface AuthorizationModelsInterface extends IndexedCollectionInterface
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
     * @return null|AuthorizationModelInterface
     */
    public function current(): ?AuthorizationModelInterface;

    /**
     * @return AuthorizationModelsShape
     */
    public function jsonSerialize(): array;

    /**
     * Get an authorization model by offset.
     *
     * @param mixed $offset
     *
     * @return null|AuthorizationModelInterface
     */
    public function offsetGet(mixed $offset): ?AuthorizationModelInterface;
}
