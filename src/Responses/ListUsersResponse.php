<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\{Users, UsersInterface};
use OpenFGA\Models\UserInterface;
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ListUsersResponse extends Response implements ListUsersResponseInterface
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
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListUsersResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(Users::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        // Handle network errors
        return RequestManager::handleResponseException(
            response: $response,
            request: $request,
        );
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
