<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Userset extends Model implements UsersetInterface
{
    public function __construct(
        public ?DirectUserset $direct = null,
        public ?ObjectRelation $computedUserset = null,
        public ?TupleToUsersetV1 $tupleToUserset = null,
        public ?Usersets $union = null,
        public ?Usersets $intersection = null,
        public ?DifferenceV1 $difference = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'direct' => $this->direct?->toArray(),
            'computed_userset' => $this->computedUserset?->toArray(),
            'tuple_to_userset' => $this->tupleToUserset?->toArray(),
            'union' => $this->union?->toArray(),
            'intersection' => $this->intersection?->toArray(),
            'difference' => $this->difference?->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            direct: isset($data['direct']) ? DirectUserset::fromArray($data['direct']) : null,
            computedUserset: isset($data['computed_userset']) ? ObjectRelation::fromArray($data['computed_userset']) : null,
            tupleToUserset: isset($data['tuple_to_userset']) ? TupleToUsersetV1::fromArray($data['tuple_to_userset']) : null,
            union: isset($data['union']) ? Usersets::fromArray($data['union']) : null,
            intersection: isset($data['intersection']) ? Usersets::fromArray($data['intersection']) : null,
            difference: isset($data['difference']) ? DifferenceV1::fromArray($data['difference']) : null,
        );
    }
}
