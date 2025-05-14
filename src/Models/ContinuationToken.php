<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ContinuationToken implements ContinuationTokenInterface
{
    public function __construct(
        private string $token,
    ) {
    }

    public function __toString(): string
    {
        return $this->getToken();
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function jsonSerialize(): string
    {
        return $this->getToken();
    }
}
