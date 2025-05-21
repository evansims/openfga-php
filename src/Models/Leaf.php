<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Models\Collections\{UsersList, UsersListInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

final class Leaf implements LeafInterface
{
    public const OPENAPI_MODEL = 'Leaf';

    private static ?SchemaInterface $schema = null;

    /**
     * @param null|UsersListInterface<UsersListUserInterface> $users
     * @param null|ComputedInterface                          $computed
     * @param null|UsersetTreeTupleToUsersetInterface         $tupleToUserset
     */
    public function __construct(
        private readonly ?UsersListInterface $users = null,
        private readonly ?ComputedInterface $computed = null,
        private readonly ?UsersetTreeTupleToUsersetInterface $tupleToUserset = null,
    ) {
        if (! $users instanceof UsersListInterface && ! $computed instanceof ComputedInterface && ! $tupleToUserset instanceof UsersetTreeTupleToUsersetInterface) {
            throw new InvalidArgumentException('Leaf must contain at least one of users, computed or tupleToUserset');
        }
    }

    #[Override]
    public function getComputed(): ?ComputedInterface
    {
        return $this->computed;
    }

    #[Override]
    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface
    {
        return $this->tupleToUserset;
    }

    #[Override]
    public function getUsers(): ?UsersListInterface
    {
        return $this->users;
    }

    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'users' => $this->users?->jsonSerialize(),
            'computed' => $this->computed?->jsonSerialize(),
            'tupleToUserset' => $this->tupleToUserset?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'users', type: UsersList::class, required: false),
                new SchemaProperty(name: 'computed', type: Computed::class, required: false),
                new SchemaProperty(name: 'tupleToUserset', type: UsersetTreeTupleToUserset::class, required: false),
            ],
        );
    }
}
