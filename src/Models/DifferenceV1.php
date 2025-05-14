<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

use function is_array;

final class DifferenceV1 implements DifferenceV1Interface
{
    public const string OPENAPI_TYPE = 'v1.Difference';

    public function __construct(
        private UsersetInterface $base,
        private UsersetInterface $subtract,
    ) {
    }

    public function getBase(): UsersetInterface
    {
        return $this->base;
    }

    public function getSubtract(): UsersetInterface
    {
        return $this->subtract;
    }

    public function jsonSerialize(): array
    {
        return [
            'base' => $this->getBase()->jsonSerialize(),
            'subtract' => $this->getSubtract()->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedDifferenceShape($data);

        return new self(
            base: Userset::fromArray($data['base']),
            subtract: Userset::fromArray($data['subtract']),
        );
    }

    /**
     * Validates the shape of the array to be used as difference data. Throws an exception if the data is invalid.

     *
     * @param array{base: UsersetShape, subtract: UsersetShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return DifferenceShape
     */
    public static function validatedDifferenceShape(array $data): array
    {
        if (! is_array($data)
            || ! isset($data['base'], $data['subtract'])
            || ! is_array($data['base'])
            || ! is_array($data['subtract'])
        ) {
            throw new InvalidArgumentException('Invalid difference data structure');
        }

        return $data;
    }
}
