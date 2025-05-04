<?php

declare(strict_types=1);

namespace OpenFGA\Models;

final class Wildcard extends Model implements WildcardInterface
{
    public function __construct(
    ) {
    }

    public function toArray(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        return new self();
    }
}
