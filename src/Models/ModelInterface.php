<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

interface ModelInterface extends JsonSerializable
{
    /**
     * Convert the model instance to an array representation suitable for JSON serialization.
     *
     * @return array The array representation of the model.
     *
     * @see https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize(): array;

    /**
     * Convert the model instance to an array representation.
     *
     * @return array The array representation of the model.
     */
    public function toArray(): array;

    /**
     * Creates a new instance of the model from an array representation.
     *
     * @param array $data The array representation of the model.
     *
     * @return static A new instance of the model.
     */
    public static function fromArray(array $data): self;
}
