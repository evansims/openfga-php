<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class User implements UserInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ?object $object = null,
        private readonly ?UsersetUserInterface $userset = null,
        private readonly ?TypedWildcardInterface $wildcard = null,
    ) {
    }

    public function getObject(): ?object
    {
        return $this->object;
    }

    public function getUserset(): ?UsersetUserInterface
    {
        return $this->userset;
    }

    public function getWildcard(): ?TypedWildcardInterface
    {
        return $this->wildcard;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'object' => $this->serializeObject(),
            'userset' => $this->userset?->jsonSerialize(),
            'wildcard' => $this->wildcard?->jsonSerialize(),
        ], static fn ($value) => null !== $value);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'object', type: 'object', required: false),
                new SchemaProperty(name: 'userset', type: UsersetUser::class, required: false),
                new SchemaProperty(name: 'wildcard', type: TypedWildcard::class, required: false),
                new SchemaProperty(name: 'difference', type: DifferenceV1::class, required: false),
            ],
        );
    }

    private function serializeObject(): mixed
    {
        if (null === $this->object) {
            return null;
        }

        if ($this->object instanceof JsonSerializable) {
            return $this->object->jsonSerialize();
        }

        if (method_exists($this->object, '__toString')) {
            return (string) $this->object;
        }

        return get_object_vars($this->object);
    }
}
