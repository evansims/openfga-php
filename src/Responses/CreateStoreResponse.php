<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;

final class CreateStoreResponse extends Response
{
    public function __construct(
        public string $id,
        public string $name,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:sP'),
            'updated_at' => $this->updatedAt->format('Y-m-d\TH:i:sP'),
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            name: $data['name'],
            createdAt: new DateTime($data['created_at']),
            updatedAt: new DateTime($data['updated_at']),
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

        if ($response->getStatusCode() === 201) {
            if (!isset($data['id'], $data['name'], $data['created_at'], $data['updated_at'])) {
                throw new \Exception('POST /stores failed');
            }

            return new static(
                id: $data['id'],
                name: $data['name'],
                createdAt: new DateTime($data['created_at']),
                updatedAt: new DateTime($data['updated_at']),
            );
        }

        Response::handleResponseException($response);

        throw new \Exception('POST /stores failed');
    }
}
