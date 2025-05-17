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
        $response = [];

        if (null !== $obj = $this->getObject()) {
            $response['object'] = $obj instanceof JsonSerializable
                ? $obj->jsonSerialize()
                : (method_exists($obj, '__toString') ? (string) $obj : get_object_vars($obj));
        }

        if (null !== $this->getUserset()) {
            $response['userset'] = $this->getUserset()->jsonSerialize();
        }

        if (null !== $this->getWildcard()) {
            $response['wildcard'] = $this->getWildcard()->jsonSerialize();
        }

        if (null !== $this->getDifference()) {
            $response['difference'] = $this->getDifference()->jsonSerialize();
        }

        return $response;
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
}
