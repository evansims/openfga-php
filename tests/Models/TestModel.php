<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Models;

use OpenFGA\Models\Model;
use OpenFGA\Models\ModelInterface;

class TestModel extends Model implements ModelInterface
{
    public function __construct(
        private string $property1 = 'test1',
        private int $property2 = 123
    ) {}

    public function getProperty1(): string
    {
        return $this->property1;
    }

    public function getProperty2(): int
    {
        return $this->property2;
    }

    public function toArray(): array
    {
        return [
            'property1' => $this->getProperty1(),
            'property2' => $this->getProperty2(),
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
