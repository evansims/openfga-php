<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * Provides JSON serialization for keyed collections.
 *
 * Classes using this trait must have a property named $models
 * containing an associative array or traversable of model objects
 * that implement JsonSerializable.
 */
trait KeyedCollectionTrait
{
    use CollectionTrait;

    public function jsonSerialize(): array
    {
        $response = [];

        foreach ($this->models as $key => $model) {
            $response[$key] = $model->jsonSerialize();
        }

        return $response;
    }
}
