<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UsersetTreeTupleToUserset extends Model implements UsersetTreeTupleToUsersetInterface
{
    public function __construct(
        private Node $base,
        private Node $subtract,
    ) {
    }

    public function getBase(): Node
    {
        return $this->base;
    }

    public function getSubtract(): Node
    {
        return $this->subtract;
    }

    public function toArray(): array
    {
        return [
            'base' => $this->base->toArray(),
            'subtract' => $this->subtract->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            base: Node::fromArray($data['base']),
            subtract: Node::fromArray($data['subtract']),
        );
    }
}
