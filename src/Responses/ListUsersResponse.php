<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Collections\{Users, UsersInterface};
use OpenFGA\Models\UserInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};

use Override;

use function is_array;

final class ListUsersResponse implements ListUsersResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * @param UsersInterface<UserInterface> $users
     */
    public function __construct(
        private UsersInterface $users,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUsers(): UsersInterface
    {
        return $this->users;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function fromResponse(\Psr\Http\Message\ResponseInterface $response, SchemaValidator $validator): ListUsersResponseInterface
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $exception) {
            throw new ApiUnexpectedResponseException($exception->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Users::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        RequestManager::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'users', type: Users::class, required: true),
            ],
        );
    }
}
