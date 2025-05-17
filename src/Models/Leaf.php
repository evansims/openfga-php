<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class Leaf implements LeafInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?UsersListInterface $users = null,
        private ?ComputedInterface $computed = null,
        private ?UsersetTreeTupleToUsersetInterface $tupleToUserset = null,
    ) {
        if (null === $users && null === $computed && null === $tupleToUserset) {
            throw new InvalidArgumentException('Leaf must contain at least one of users, computed or tupleToUserset');
        }
    }

    public function getComputed(): ?ComputedInterface
    {
        return $this->computed;
    }

    public function getTupleToUserset(): ?UsersetTreeTupleToUsersetInterface
    {
        return $this->tupleToUserset;
    }

    public function getUsers(): ?UsersListInterface
    {
        return $this->users;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'users' => $this->users?->jsonSerialize(),
            'computed' => $this->computed?->jsonSerialize(),
            'tupleToUserset' => $this->tupleToUserset?->jsonSerialize(),
        ], static fn ($value) => null !== $value);
    }

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
