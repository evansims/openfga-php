<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @extends AbstractIndexedCollection<AuthorizationModel>
 */
final class AuthorizationModels extends AbstractIndexedCollection implements AuthorizationModelsInterface
{
    protected static string $itemType = AuthorizationModel::class;

    /**
     * @return null|AuthorizationModelInterface
     */
    public function current(): ?AuthorizationModelInterface
    {
        if (! $this->valid()) {
            return null;
        }

        $key = $this->key();
        if (null === $key) {
            return null;
        }

        /** @var null|AuthorizationModelInterface $result */
        return $this->offsetGet($key);
    }

    /**
     * @param mixed $offset
     *
     * @return null|AuthorizationModelInterface
     */
    public function offsetGet(mixed $offset): ?AuthorizationModelInterface
    {
        /** @var null|AuthorizationModelInterface $result */
        $result = parent::offsetGet($offset);

        return $result instanceof AuthorizationModelInterface ? $result : null;
    }
}
