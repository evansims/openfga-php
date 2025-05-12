<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class TypedWildcard extends Model implements TypedWildcardInterface
{
    public function __construct(
        private string $type,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
        );
    }
}
