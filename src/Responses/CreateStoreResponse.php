<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class CreateStoreResponse implements CreateStoreResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private string $id,
        private string $name,
        private DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['id']) && is_string($data['id']));
        assert(isset($data['name']) && is_string($data['name']));
        assert(isset($data['created_at']) && is_string($data['created_at']));
        assert(isset($data['updated_at']) && is_string($data['updated_at']));

        return new self(
            id: $data['id'],
            name: $data['name'],
            createdAt: new DateTimeImmutable($data['created_at']),
            updatedAt: new DateTimeImmutable($data['updated_at']),
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

        if (201 === $response->getStatusCode() && is_array($data)) {
            return self::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
