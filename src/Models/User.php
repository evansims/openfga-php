<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty};

use Override;

final class User implements UserInterface
{
    private static ?SchemaInterface $schema = null;

    public function __construct(
        private readonly ?object $object = null,
        private readonly ?UsersetUserInterface $userset = null,
        private readonly ?TypedWildcardInterface $wildcard = null,
        private readonly ?DifferenceV1Interface $difference = null,
    ) {
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getDifference(): ?DifferenceV1Interface
    {
        return $this->difference;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getObject(): ?object
    {
        return $this->object;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getUserset(): ?UsersetUserInterface
    {
        return $this->userset;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getWildcard(): ?TypedWildcardInterface
    {
        return $this->wildcard;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'object' => $this->serializeObject(),
            'userset' => $this->userset?->jsonSerialize(),
            'wildcard' => $this->wildcard?->jsonSerialize(),
            'difference' => $this->difference?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);
    }

    #[Override]
    /**
     * @inheritDoc
     */
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
