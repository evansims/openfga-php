<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Models;

use OpenFGA\Models\Model;
use OpenFGA\Models\ModelInterface;

class AnotherTestModel extends Model implements ModelInterface
{
    public function __construct(
        private string $name = 'test'
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? 'default'
        );
    }
}
