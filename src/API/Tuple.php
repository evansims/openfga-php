<?php

declare(strict_types=1);

namespace OpenFGA\API;

final class Tuple
{
    public function __construct(
        private ?string $user,
        private ?string $relation,
        private ?string $object,
    ) {
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setRelation(?string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'relation' => $this->relation,
            'object' => $this->object,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            user: $data['user'] ?? null,
            relation: $data['relation'] ?? null,
            object: $data['object'] ?? null,
        );
    }
}
