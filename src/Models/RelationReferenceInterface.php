<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type RelationReferenceShape = array{type: string, relation?: string, wildcard?: object, condition?: string}
 */
interface RelationReferenceInterface extends ModelInterface
{
    public function getCondition(): ?string;

    public function getRelation(): ?string;

    public function getType(): string;

    public function getWildcard(): ?object;

    /**
     * @return RelationReferenceShape
     */
    public function jsonSerialize(): array;

    /**
     * @param RelationReferenceShape $data
     */
    public static function fromArray(array $data): static;
}
