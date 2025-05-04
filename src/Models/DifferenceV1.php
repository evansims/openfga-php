<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use InvalidArgumentException;

final class DifferenceV1 extends Model implements DifferenceV1Interface
{
    public function __construct(
        public Userset $base,
        public Userset $subtract,
    ) {
    }

    public function toArray(): array
    {
        return [
            'base' => $this->base->toArray(),
            'subtract' => $this->subtract->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        $base = $data['base'] ?? throw new InvalidArgumentException('Missing base');
        $subtract = $data['subtract'] ?? throw new InvalidArgumentException('Missing subtract');

        return new self(
            base: Userset::fromArray($base),
            subtract: Userset::fromArray($subtract),
        );
    }
}
