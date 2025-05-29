<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\Collections\{Users, UsersInterface};
use OpenFGA\Models\Collections\Usersets;
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, TypedWildcard, User, UserInterface, UserObject, Userset, UsersetUser};
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUsers(): UsersInterface
    {
        return $this->users;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ListUsersResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            $validator->registerSchema(UserObject::schema());
            $validator->registerSchema(UsersetUser::schema());
            $validator->registerSchema(TypedWildcard::schema());
            $validator->registerSchema(ObjectRelation::schema());
            $validator->registerSchema(TupleToUsersetV1::schema());
            $validator->registerSchema(Usersets::schema());
            $validator->registerSchema(Userset::schema());
            $validator->registerSchema(DifferenceV1::schema());
            $validator->registerSchema(User::schema());
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

    /**
     * @inheritDoc
     */
    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'users', type: 'object', className: Users::class, required: true),
            ],
        );
    }
}
