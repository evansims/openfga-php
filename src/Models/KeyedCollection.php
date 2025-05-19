<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Collection implementation that preserves keys when serialized.
 *
 * @template T of ModelInterface
 *
 * @extends Collection<T>
 * @implements KeyedCollectionInterface<T>
 */
class KeyedCollection extends Collection implements KeyedCollectionInterface
{
    public function jsonSerialize(): array
    {
        $response = [];

        foreach ($this->models as $key => $model) {
            $response[$key] = $model->jsonSerialize();
        }

        return $response;
    }
}
