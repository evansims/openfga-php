<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Exceptions\{ClientError, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{UsersList, UsersListInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use OpenFGA\Translation\Translator;
use Override;
use ReflectionException;

final class Leaf implements LeafInterface
{
    public const string OPENAPI_MODEL = 'Leaf';

    private static ?SchemaInterface $schema = null;

    /**
     * @param UsersListInterface<UsersListUserInterface>|null $users
     * @param ComputedInterface|null                          $computed
     * @param UsersetTreeTupleToUsersetInterface|null         $tupleToUserset
     *
     * @throws ClientThrowable          If none of the leaf content parameters are provided
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private readonly ?UsersListInterface $users = null,
        private readonly ?ComputedInterface $computed = null,
        private readonly ?UsersetTreeTupleToUsersetInterface $tupleToUserset = null,
    ) {
        if (! $users instanceof UsersListInterface && ! $computed instanceof ComputedInterface && ! $tupleToUserset instanceof UsersetTreeTupleToUsersetInterface) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::MODEL_LEAF_MISSING_CONTENT)]);
        }
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
                new SchemaProperty(name: 'users', type: 'object', className: UsersList::class, required: false),
                new SchemaProperty(name: 'computed', type: 'object', className: Computed::class, required: false),
                new SchemaProperty(name: 'tupleToUserset', type: 'object', className: UsersetTreeTupleToUserset::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getComputed(): ?ComputedInterface
    {
        return $this->computed;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface
    {
        return $this->tupleToUserset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUsers(): ?UsersListInterface
    {
        return $this->users;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'users' => $this->users?->jsonSerialize(),
            'computed' => $this->computed?->jsonSerialize(),
            'tupleToUserset' => $this->tupleToUserset?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }
}
