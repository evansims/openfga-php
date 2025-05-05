<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Models;

use OpenFGA\Models\Model;
use OpenFGA\Models\ModelInterface;

class TestModel extends Model implements ModelInterface
{
    public function __construct(
        public string $property1 = 'test1',
        public int $property2 = 123
    ) {}

    public function toArray(): array
    {
        return [
            'property1' => $this->property1,
            'property2' => $this->property2,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            property1: $data['property1'] ?? 'default1',
            property2: $data['property2'] ?? 0
        );
    }
}
