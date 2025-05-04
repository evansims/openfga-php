<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class WriteAssertionsResponse extends Response
{
    public function __construct(
    ) {
    }

    public function toArray(): array
    {
        return [];
    }

    public static function fromArray(array $data): static
    {
        return new static();
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        if ($response->getStatusCode() === 204) {
            return new static();
        }

        Response::handleResponseException($response);

        throw new \Exception('PUT /stores failed');
    }
}
