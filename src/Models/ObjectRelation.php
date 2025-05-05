<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class ObjectRelation extends Model implements ObjectRelationInterface
{
    /**
     * @param null|string $object
     * @param null|string $relation
     */
    public function __construct(
        public ?string $object = null,
        public ?string $relation = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'relation' => $this->relation,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            object: $data['object'],
            relation: $data['relation'],
        );
    }
}
