<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class Computed implements ComputedInterface
{
    public function __construct(
        private string $userset,
    ) {
    }

    public function getUserset(): string
    {
        return $this->userset;
    }

    public function jsonSerialize(): array
    {
        return [
            'userset' => $this->getUserset(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedComputedShape($data);

        return new self(
            userset: $data['userset'],
        );
    }

    /**
     * Validates the shape of the array to be used as computed data. Throws an exception if the data is invalid.
     *
     * @param array{userset: string} $data
     *
     * @throws InvalidArgumentException
     *
     * @return ComputedShape
     */
    public static function validatedComputedShape(array $data): array
    {
        if (! isset($data['userset'])) {
            throw new InvalidArgumentException('Missing required field: userset');
        }

        return $data;
    }
}
