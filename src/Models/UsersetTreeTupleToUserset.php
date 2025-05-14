<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class UsersetTreeTupleToUserset implements UsersetTreeTupleToUsersetInterface
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
        $data = self::validatedUsersetTreeTupleToUsersetShape($data);

        return new self(
            base: Node::fromArray($data['base']),
            subtract: Node::fromArray($data['subtract']),
        );
    }

    /**
     * @param array{base: NodeShape, subtract: NodeShape} $data
     *
     * @throws InvalidArgumentException
     *
     * @return UsersetTreeTupleToUsersetShape
     */
    public static function validatedUsersetTreeTupleToUsersetShape(array $data): array
    {
        if (! isset($data['base'], $data['subtract'])) {
            throw new InvalidArgumentException('Missing required fields in UsersetTreeTupleToUserset');
        }

        return $data;
    }
}
