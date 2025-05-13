<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface AuthorizationModelIdInterface extends ModelInterface
{
    public function __toString(): string;

    public static function fromAuthorizationModel(AuthorizationModel $authorizationModel): self;

    public static function fromString(string $id): self;

    /**
     * @return array{
     *     id: string,
     * }
     */
    public function jsonSerialize(): array;

    /**
     * @param array{
     *     id: string,
     * } $data
     */
    public static function fromArray(array $data): static;
}
