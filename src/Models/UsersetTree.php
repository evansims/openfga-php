<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class UsersetTree implements UsersetTreeInterface
{
    public function __construct(
        private NodeInterface $root,
    ) {
    }

    public function getRoot(): NodeInterface
    {
        return $this->root;
    }

    public function jsonSerialize(): array
    {
        return [
            'root' => $this->getRoot()->jsonSerialize(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $data = self::validatedUsersetTreeShape($data);

        return new self(
            root: Node::fromArray($data['root']),
        );
    }

    /**
     * @param array{root: NodeShape} $data
     *
     * @return UsersetTreeShape
     */
    public static function validatedUsersetTreeShape(array $data): array
    {
        if (! isset($data['root'])) {
            throw new InvalidArgumentException('UsersetTree must have a root');
        }

        return $data;
    }
}
