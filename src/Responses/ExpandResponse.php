<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{Collections\Nodes, Collections\Users, Collections\UsersList, Collections\UsersetUnion, Collections\Usersets, Computed, DifferenceV1, Leaf, Node, NodeUnion, ObjectRelation, TupleToUsersetV1, UsersListUser, Userset, UsersetTree, UsersetTreeDifference, UsersetTreeInterface, UsersetTreeTupleToUserset, UsersetUser};
use OpenFGA\Network\RequestManager;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Override;

use Psr\Http\Message\{RequestInterface, ResponseInterface};

final class ExpandResponse extends Response implements ExpandResponseInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?UsersetTreeInterface $tree = null,
    ) {
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTree(): ?UsersetTreeInterface
    {
        return $this->tree;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function fromResponse(
        ResponseInterface $response,
        RequestInterface $request,
        SchemaValidator $validator,
    ): ExpandResponseInterface {
        // Handle successful responses
        if (200 === $response->getStatusCode()) {
            $data = self::parseResponse($response, $request);

            // Register all schemas needed for UsersetTree
            $validator->registerSchema(ObjectRelation::schema());
            $validator->registerSchema(TupleToUsersetV1::schema());
            $validator->registerSchema(DifferenceV1::schema());
            $validator->registerSchema(Usersets::schema());
            $validator->registerSchema(UsersetUser::schema());
            $validator->registerSchema(UsersListUser::schema());
            $validator->registerSchema(Users::schema());
            $validator->registerSchema(UsersList::schema());
            $validator->registerSchema(Userset::schema());
            $validator->registerSchema(UsersetUnion::schema());
            $validator->registerSchema(Node::schema());
            $validator->registerSchema(NodeUnion::schema());
            $validator->registerSchema(Nodes::schema());
            $validator->registerSchema(Leaf::schema());
            $validator->registerSchema(Computed::schema());
            $validator->registerSchema(UsersetTreeTupleToUserset::schema());
            $validator->registerSchema(UsersetTreeDifference::schema());
            $validator->registerSchema(UsersetTree::schema());
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
                new SchemaProperty(name: 'tree', type: 'object', className: UsersetTree::class, required: false),
            ],
        );
    }
}
