<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModelId implements AuthorizationModelIdInterface
{
    public function __construct(
        private string $id,
    ) {
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): string
    {
        return $this->id;
    }

    public static function fromAuthorizationModel(AuthorizationModel $authorizationModel): self
    {
        return new self(
            id: $authorizationModel->getId(),
        );
    }
}
