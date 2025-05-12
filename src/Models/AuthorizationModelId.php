<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class AuthorizationModelId extends Model implements AuthorizationModelIdInterface
{
    /**
     * Constructor.
     *
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }

    public static function fromAuthorizationModel(AuthorizationModel $authorizationModel): self
    {
        return new self(
            id: $authorizationModel->id,
        );
    }

    public static function fromString(string $id): self
    {
        return new self(
            id: $id,
        );
    }
}
