<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;
use function is_string;

final class CreateStoreResponse extends Response
{
    public function __construct(
        public string $id,
        public string $name,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:sP'),
            'updated_at' => $this->updatedAt->format('Y-m-d\TH:i:sP'),
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $id = isset($data['id']) && is_string($data['id']) ? $data['id'] : '';
        $name = isset($data['name']) && is_string($data['name']) ? $data['name'] : '';

        $createdAtStr = isset($data['created_at']) && is_string($data['created_at'])
            ? $data['created_at']
            : '2023-01-01T00:00:00+00:00';

        $updatedAtStr = isset($data['updated_at']) && is_string($data['updated_at'])
            ? $data['updated_at']
            : '2023-01-01T00:00:00+00:00';

        return new self(
            id: $id,
            name: $name,
            createdAt: new DateTimeImmutable($createdAtStr),
            updatedAt: new DateTimeImmutable($updatedAtStr),
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

        if (201 === $response->getStatusCode() && is_array($data) && isset($data['id']) && is_string($data['id']) && isset($data['name']) && is_string($data['name']) && isset($data['created_at']) && is_string($data['created_at']) && isset($data['updated_at']) && is_string($data['updated_at'])) {
            return new static(
                id: $data['id'],
                name: $data['name'],
                createdAt: new DateTimeImmutable($data['created_at']),
                updatedAt: new DateTimeImmutable($data['updated_at']),
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
