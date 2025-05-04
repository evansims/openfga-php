<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TupleToUsersetV1 extends Model implements TupleToUsersetV1Interface
{
    public function __construct(
        public ObjectRelation $tupleset,
        public ObjectRelation $computedUserset,
    ) {
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
