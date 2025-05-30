<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};
use Override;

use function is_array;
use function is_object;
use function is_string;

final class User implements UserInterface
{
    public const string OPENAPI_MODEL = 'User';

    private static ?SchemaInterface $schema = null;

    /**
     * @param mixed                       $object     The user object (string, UserObjectInterface, or other types)
     * @param UsersetUserInterface|null   $userset    Optional userset user specification
     * @param TypedWildcardInterface|null $wildcard   Optional wildcard type specification
     * @param DifferenceV1Interface|null  $difference Optional difference operation
     */
    public function __construct(
        private readonly mixed $object = null,
        private readonly ?UsersetUserInterface $userset = null,
        private readonly ?TypedWildcardInterface $wildcard = null,
        private readonly ?DifferenceV1Interface $difference = null,
    ) {
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
                new SchemaProperty(name: 'object', type: 'object', required: false),
                new SchemaProperty(name: 'userset', type: 'object', className: UsersetUser::class, required: false),
                new SchemaProperty(name: 'wildcard', type: 'object', className: TypedWildcard::class, required: false),
                new SchemaProperty(name: 'difference', type: 'object', className: DifferenceV1::class, required: false),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getObject(): null | UserObjectInterface | string
    {
        if (null === $this->object || is_string($this->object) || $this->object instanceof UserObjectInterface) {
            return $this->object;
        }

        // Handle plain object with type and id properties
        if (is_object($this->object) && property_exists($this->object, 'type') && property_exists($this->object, 'id')) {
            /** @var mixed $type */
            $type = $this->object->type;

            /** @var mixed $id */
            $id = $this->object->id;
            if (is_string($type) && is_string($id)) {
                return new UserObject(
                    type: $type,
                    id: $id,
                );
            }
        }

        // Handle array with type and id properties (from JSON decode)
        if (is_array($this->object) && isset($this->object['type'], $this->object['id'])) {
            /** @var mixed $type */
            $type = $this->object['type'];

            /** @var mixed $id */
            $id = $this->object['id'];
            if (is_string($type) && is_string($id)) {
                return new UserObject(
                    type: $type,
                    id: $id,
                );
            }
        }

        // If it's an object that can be converted to string
        if (is_object($this->object) && method_exists($this->object, '__toString')) {
            return (string) $this->object;
        }

        // For legacy/test purposes, if it's a plain object with an id property, return it as a string
        if (is_object($this->object) && property_exists($this->object, 'id') && is_string($this->object->id)) {
            return $this->object->id;
        }

        // For any other case, return null since we can't convert it
        return null;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getUserset(): ?UsersetUserInterface
    {
        return $this->userset;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getWildcard(): ?TypedWildcardInterface
    {
        return $this->wildcard;
    }

    /**
     * @inheritDoc
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return array_filter([
            'object' => $this->serializeObject(),
            'userset' => $this->userset?->jsonSerialize(),
            'wildcard' => $this->wildcard?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    /**
     * Serializes the object property for JSON representation.
     *
     * Handles various object types including JsonSerializable objects,
     * objects with __toString methods, and plain objects.
     *
     * @return mixed The serialized object representation
     */
    private function serializeObject(): mixed
    {
        if (null === $this->object) {
            return null;
        }

        if ($this->object instanceof JsonSerializable) {
            return $this->object->jsonSerialize();
        }

        if (is_object($this->object) && method_exists($this->object, '__toString')) {
            return (string) $this->object;
        }

        if (is_object($this->object)) {
            return get_object_vars($this->object);
        }

        // For arrays or other types, return as-is
        return $this->object;
    }
}
