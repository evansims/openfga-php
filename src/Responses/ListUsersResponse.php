<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Users, UsersInterface, UsersetUserInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

/**
 * @implements ListUsersResponseInterface<array{users: array{object: array{type: string, id: string}, relation: string, users: array{users: array{object: array{type: string, id: string}, relation: string, user: string}[]}}}>
 */
final class ListUsersResponse implements ListUsersResponseInterface
{
    use ResponseTrait;

    private static ?SchemaInterface $schema = null;

    public function __construct(
        private UsersInterface $users,
    ) {
    }

    public function getUsers(): UsersInterface
    {
        return $this->users;
    }

    /**
     * @return array{users: array{object: array{type: string, id: string}, relation: string, users: array{users: array<array{object: array{type: string, id: string}, relation: string, user: string}>}}}
     */
    public function toArray(): array
    {
        $result = [
            'object' => [
                'type' => '',
                'id' => '',
            ],
            'relation' => '',
            'users' => [
                'users' => [],
            ],
        ];

        foreach ($this->users as $user) {
            if (null === $user) {
                continue;
            }

            $object = $user->getObject();
            $userset = $user->getUserset();

            if (null === $object || null === $userset) {
                continue;
            }

            $objectType = $object->getType();
            $objectId = $object->getId();

            $users = $this->extractUsersFromUserset($userset);

            $result = [
                'object' => [
                    'type' => $objectType,
                    'id' => $objectId,
                ],
                'relation' => (string) $userset->getRelation(),
                'users' => [
                    'users' => $users,
                ],
            ];

            // Only process the first user as we're returning a single result
            break;
        }

        /** @var array{users: array{object: array{type: string, id: string}, relation: string, users: array{users: array<array{object: array{type: string, id: string}, relation: string, user: string}>}}} $finalResult */
        return ['users' => $result];
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Users::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'users', type: Users::class, required: true),
            ],
        );
    }

    /**
     * @param UsersetUserInterface $userset
     *
     * @return array<array{object: array{type: string, id: string}, relation: string, user: string}>
     */
    private function extractUsersFromUserset(UsersetUserInterface $userset): array
    {
        return [
            [
                'object' => [
                    'type' => $userset->getType(),
                    'id' => $userset->getId(),
                ],
                'relation' => $userset->getRelation(),
                'user' => $userset->getId(),
            ],
        ];
    }
}
