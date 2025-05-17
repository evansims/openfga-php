<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;

use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

final class User implements UserInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private ?object $object = null,
        private ?UsersetUserInterface $userset = null,
        private ?TypedWildcardInterface $wildcard = null,
        private ?DifferenceV1Interface $difference = null,
    ) {
    }

    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
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
            'difference' => $this->difference?->jsonSerialize(),
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

    private function serializeObject(): object | string | array | null
    {
        if ($this->object instanceof JsonSerializable) {
            return $this->object->jsonSerialize();
        }

        if (method_exists($this->object, '__toString')) {
            return (string) $this->object;
        }

        return get_object_vars($this->object);
    }
}
