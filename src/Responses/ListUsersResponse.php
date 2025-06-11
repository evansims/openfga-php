<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use InvalidArgumentException;
use OpenFGA\Exceptions\{NetworkException, SerializationException};
use OpenFGA\Models\Collections\{Users, UsersInterface, Usersets};
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, TypedWildcard, User, UserObject, Userset, UsersetUser};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schemas\{Schema, SchemaInterface, SchemaProperty, SchemaValidatorInterface};
use Override;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};
use ReflectionException;

/**
 * Response containing a list of users that have a specific relationship with an object.
 *
 * This response provides a collection of users (including user objects, usersets,
 * and typed wildcards) that have the specified relationship with the target object.
 * Use this to discover who has access to resources in your authorization system.
 *
 * @see ListUsersResponseInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/ListUsers
 */
final class ListUsersResponse extends Response implements ListUsersResponseInterface
{
    private static ?SchemaInterface $schema = null;

    /**
     * Create a new list users response instance.
     *
     * @param UsersInterface $users The collection of users that have the specified relationship with the object
     */
    public function __construct(
        private readonly UsersInterface $users,
    ) {
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws NetworkException         If the API returns an error response
     * @throws ReflectionException      If exception location capture fails
     * @throws SerializationException   If JSON parsing or schema validation fails
     */
    #[Override]
    public static function fromResponse(
        HttpResponseInterface $response,
        HttpRequestInterface $request,
        SchemaValidatorInterface $validator,
    ): ListUsersResponseInterface {
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

        RequestManager::handleResponseException(
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

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUsers(): UsersInterface
    {
        return $this->users;
    }
}
