<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;
use function is_bool;
use function is_string;

final class CheckResponse extends Response
{
    public function __construct(
        public bool $allowed,
        public string $resolution,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'allowed' => $this->allowed,
            'resolution' => $this->resolution,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            allowed: $data['allowed'],
            resolution: $data['resolution'],
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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['allowed']) && is_bool($data['allowed']) && isset($data['resolution']) && is_string($data['resolution'])) {
            return new static(
                allowed: $data['allowed'],
                resolution: $data['resolution'],
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
