<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * A DirectUserset is a sentinel message for referencing the direct members specified by an object/relation mapping.
 */
final class DirectUserset extends Model implements DirectUsersetInterface
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
