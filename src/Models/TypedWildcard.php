<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

use function is_string;

final class TypedWildcard implements TypedWildcardInterface
{
    public function __construct(
        private string $type,
    ) {
    }

    public function __toString(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): string
    {
        return $this->getType();
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedTypedWildcardShape($data);

        return new self(
            type: $data['type'],
        );
    }

    /**
     * Validates the shape of the array to be used as typed wildcard data. Throws an exception if the data is invalid.
     *
     * @param array{type: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return TypedWildcardShape
     */
    public static function validatedTypedWildcardShape(array $data): array
    {
        if (! isset($data['type'])) {
            throw new InvalidArgumentException('Missing required field "type"');
        }

        if (! is_string($data['type'])) {
            throw new InvalidArgumentException('Field "type" must be a string');
        }

        return $data;
    }
}
