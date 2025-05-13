<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModels implements AuthorizationModelsInterface
{
    use CollectionTrait;

    public function add(AuthorizationModelInterface $authorizationModel): void
    {
        $this->models[] = $authorizationModel;
    }

    public function current(): AuthorizationModelInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?AuthorizationModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $model) {
            $collection->add(AuthorizationModel::fromArray($model));
        }

        return $collection;
    }
}
