<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class CheckResponse implements CheckResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private ?bool $allowed = null,
        private ?string $resolution = null,
    ) {
    }

    public function getAllowed(): ?bool
    {
        return $this->allowed;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public static function fromArray(array $data): static
    {
        return new self(
            allowed: $data['allowed'] ?? null,
            resolution: $data['resolution'] ?? null,
        );
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            return self::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
