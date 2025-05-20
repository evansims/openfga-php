<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

interface AccessTokenInterface
{
    public function __toString(): string;

    public function getExpires(): int;

    public function getScope(): ?string;

    public function getToken(): string;

    public function isExpired(): bool;

    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response): self;
}
