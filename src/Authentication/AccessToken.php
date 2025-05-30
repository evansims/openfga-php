<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

use Exception;

use Override;

use function is_array;
use function is_int;
use function is_string;

final class AccessToken implements AccessTokenInterface
{
    public function __construct(
        private string $token,
        private int $expires,
        private ?string $scope = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function isExpired(): bool
    {
        return $this->expires < time();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response): self
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($data)) {
            throw new Exception('Invalid response format');
        }

        if (! isset($data['access_token'], $data['expires_in'])) {
            throw new Exception('Missing required fields in response');
        }

        if (! is_string($data['access_token'])) {
            throw new Exception('access_token must be a string');
        }

        if (! is_int($data['expires_in'])) {
            throw new Exception('expires_in must be an integer');
        }

        return new self(
            token: $data['access_token'],
            expires: time() + $data['expires_in'],
            scope: isset($data['scope']) && is_string($data['scope']) ? $data['scope'] : null,
        );
    }
}
