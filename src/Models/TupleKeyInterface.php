<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type TupleKeyShape = array{user?: string, relation?: string, object?: string, condition?: ConditionShape}
 */
interface TupleKeyInterface extends ModelInterface
{
    public function getCondition(): ?ConditionInterface;

    public function getObject(): ?string;

    public function getRelation(): ?string;

    public function getUser(): ?string;

    /**
     * @return TupleKeyShape
     */
    public function jsonSerialize(): array;

    /**
     * @param TupleKeyType  $type
     * @param TupleKeyShape $data
     */
    public static function fromArray(TupleKeyType $type, array $data): static;
}
