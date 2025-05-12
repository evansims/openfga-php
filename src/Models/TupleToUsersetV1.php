<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleToUsersetV1 extends Model implements TupleToUsersetV1Interface
{
    public function __construct(
        private ObjectRelation $tupleset,
        private ObjectRelation $computedUserset,
    ) {
    }

    public function getComputedUserset(): ObjectRelation
    {
        return $this->computedUserset;
    }

    public function getTupleset(): ObjectRelation
    {
        return $this->tupleset;
    }

    public function toArray(): array
    {
        return [
            'tupleset' => $this->tupleset->toArray(),
            'computed_userset' => $this->computedUserset->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            tupleset: ObjectRelation::fromArray($data['tupleset']),
            computedUserset: ObjectRelation::fromArray($data['computed_userset']),
        );
    }
}
