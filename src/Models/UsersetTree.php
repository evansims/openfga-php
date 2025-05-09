<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class UsersetTree extends Model implements UsersetTreeInterface
{
    public function __construct(
        private Node $node,
    ) {
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function toArray(): array
    {
        return [
            'node' => $this->node->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            node: Node::fromArray($data['node']),
        );
    }
}
