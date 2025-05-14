<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type UserTypeFilterShape = array{type: string, relation?: string}
 */
interface UserTypeFilterInterface extends ModelInterface
{
    public function getRelation(): ?string;

    public function getType(): string;

    /**
     * @return UserTypeFilterShape
     */
    public function jsonSerialize(): array;

    /**
     * @param UserTypeFilterShape $data
     */
    public static function fromArray(array $data): static;
}
