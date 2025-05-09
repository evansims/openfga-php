<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use function assert;

final class UsersetTreeDifference extends Model implements UsersetTreeDifferenceInterface
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

    public function toArray(): array
    {
        return [
            'base' => $this->base->toArray(),
            'subtract' => $this->subtract->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['base'], $data['subtract']));

        return new self(
            base: Node::fromArray($data['base']),
            subtract: Node::fromArray($data['subtract']),
        );
    }
}
