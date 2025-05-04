<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

final class AccessToken
{
    public function __construct(
        private string $token,
        private int $expires,
        private ?string $scope = null,
    ) {
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpires(): int
    {
        return $this->expires;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function isExpired(): bool
    {
        return $this->expires < time();
    }

    public function __toString(): string
    {
        return $this->token;
    }
}
