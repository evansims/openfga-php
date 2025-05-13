<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Computed implements ComputedInterface
{
    use ModelTrait;

    public function __construct(
        private string $userset,
    ) {
    }

    public function getUserset(): string
    {
        return $this->userset;
    }

    public function jsonSerialize(): array
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
