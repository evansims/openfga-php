<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeInterface;
use Override;

interface StoreInterface extends ModelInterface
{
    public function getCreatedAt(): DateTimeInterface;

    public function getDeletedAt(): ?DateTimeInterface;

    public function getId(): string;

    public function getName(): string;

    public function getUpdatedAt(): DateTimeInterface;

    /**
     * @return array<'created_at'|'deleted_at'|'id'|'name'|'updated_at', string>
     */
    #[Override]
    public function jsonSerialize(): array;
}
