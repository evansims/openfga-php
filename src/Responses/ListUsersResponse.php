<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use InvalidArgumentException;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Users, UsersInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ListUsersResponse implements ListUsersResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private UsersInterface $users,
    ) {
    }

    public function getUsers(): UsersInterface
    {
        return $this->users;
    }

    public static function fromArray(array $data): static
    {
        $data = self::validatedUsersResponse($data);

        return new self(
            users: Users::fromArray($data['users']),
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
            return static::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function validatedUsersResponse(array $data): array
    {
        if (! isset($data['users']) || ! is_array($data['users'])) {
            throw new InvalidArgumentException('Users must be an array');
        }

        return $data;
    }
}
