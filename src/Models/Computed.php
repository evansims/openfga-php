<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Computed extends Model implements ComputedInterface
{
    public function __construct(
        private string $userset,
    ) {
    }

    public function getUserset(): string
    {
        return $this->userset;
    }

    public function toArray(): array
    {
        return [
            'userset' => $this->userset,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userset: $data['userset'],
        );
    }
}
