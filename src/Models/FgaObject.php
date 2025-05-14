<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class FgaObject implements FgaObjectInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedObjectShape($data);

        return new self(
            id: $data['id'],
        );
    }

    /**
     * Validates the shape of the array to be used as object data. Throws an exception if the data is invalid.
     *
     * @param array{id: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return FgaObjectShape
     */
    public static function validatedObjectShape(array $data): array
    {
        if (! isset($data['id'])) {
            throw new InvalidArgumentException('Object must have an id');
        }

        return $data;
    }
}
