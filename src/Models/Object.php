<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use function assert;

final class Object extends Model implements ObjectInterface
{
    /**
     * Constructor.
     *
     * @param string $id The store id.
     */
    public function __construct(
        private string $id,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return $this->getId();
    }

    public static function fromArray(array $data): self
    {
        assert(isset($data['id']));

        return new self(
            id: $data['id'],
        );
    }
}
