<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class User extends Model implements UserInterface
{
    public function __construct(
        private ?object $object = null,
        private ?UsersetUser $userset = null,
        private ?TypedWildcard $wildcard = null,
        private ?DifferenceV1 $difference = null,
    ) {
    }

    public function getDifference(): ?DifferenceV1
    {
        return $this->difference;
    }

    public function getUser(): ?object
    {
        return $this->object;
    }

    public function getUserset(): ?UsersetUser
    {
        return $this->userset;
    }

    public function getWildcard(): ?TypedWildcard
    {
        return $this->wildcard;
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object?->toArray(),
            'userset' => $this->userset?->toArray(),
            'wildcard' => $this->wildcard?->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            object: isset($data['object']) ? Object::fromArray($data['object']) : null,
            userset: isset($data['userset']) ? Userset::fromArray($data['userset']) : null,
            wildcard: isset($data['wildcard']) ? TypedWildcard::fromArray($data['wildcard']) : null,
        );
    }
}
