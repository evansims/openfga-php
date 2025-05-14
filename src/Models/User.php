<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class User implements UserInterface
{
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

        if (null !== $this->getObject()) {
            $response['object'] = $this->getObject();
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

    public static function fromArray(array $data): self
    {
        $data = self::validatedUserShape($data);

        return new self(
            object: isset($data['object']) ? Object::fromArray($data['object']) : null,
            userset: isset($data['userset']) ? Userset::fromArray($data['userset']) : null,
            wildcard: isset($data['wildcard']) ? TypedWildcard::fromArray($data['wildcard']) : null,
        );
    }

    /**
     * @param array{object?: object, userset?: UsersetUserShape, wildcard?: TypedWildcardShape, difference?: DifferenceV1Shape} $data
     *
     * @return UserShape
     */
    public static function validatedUserShape(array $data): array
    {
        return $data;
    }
}
