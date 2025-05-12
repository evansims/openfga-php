<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use function assert;

final class UserTypeFilter extends Model implements UserTypeFilterInterface
{
    public function __construct(
        private string $type,
        private string $relation,
    ) {
    }

    public function getRelation(): string
    {
        return $this->relation;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'relation' => $this->relation,
        ];
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['type'], $data['relation']));

        return new self(
            type: $data['type'],
            relation: $data['relation'],
        );
    }
}
