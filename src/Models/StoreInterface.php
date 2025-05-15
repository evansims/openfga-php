<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

/**
 * @psalm-type StoreShape = array{id: string, name: string, created_at: string, updated_at: string, deleted_at?: string}
 */
interface StoreInterface extends ModelInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getDeletedAt(): ?DateTimeImmutable;

    public function getId(): string;

    public function getName(): string;

    public function getUpdatedAt(): DateTimeImmutable;

    /**
     * @return StoreShape
     */
    public function jsonSerialize(): array;
}
