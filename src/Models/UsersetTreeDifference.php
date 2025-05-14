<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class UsersetTreeDifference implements UsersetTreeDifferenceInterface
{
    public function __construct(
        private NodeInterface $base,
        private NodeInterface $subtract,
    ) {
    }

    public function getBase(): NodeInterface
    {
        return $this->base;
    }

    public function getSubtract(): NodeInterface
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
        $data = self::validatedUsersetTreeDifferenceShape($data);

        return new self(
            base: Node::fromArray($data['base']),
            subtract: Node::fromArray($data['subtract']),
        );
    }

    /**
     * Validates the shape of the array to be used as userset tree difference data. Throws an exception if the data is invalid.
     *
     * @param array{base: NodeShape, subtract: NodeShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return UsersetTreeDifferenceShape
     */
    public static function validatedUsersetTreeDifferenceShape(array $data): array
    {
        if (! isset($data['base'], $data['subtract'])) {
            throw new InvalidArgumentException('Missing required fields in UsersetTreeDifferenceShape');
        }

        return $data;
    }
}
