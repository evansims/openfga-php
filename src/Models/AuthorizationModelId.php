<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModelId implements AuthorizationModelIdInterface
{
    use ModelTrait;

    /**
     * @param string $id The Authorization Model ID.
     */
    public function __construct(
        private string $id,
    ) {
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['id']), 'Missing id');

        return new self(
            id: $data['id'],
        );
    }

    public static function fromAuthorizationModel(AuthorizationModel $authorizationModel): self
    {
        return new self(
            id: $authorizationModel->getId(),
        );
    }

    public static function fromString(string $id): self
    {
        return new self(
            id: $id,
        );
    }
}
