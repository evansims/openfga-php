<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeInterface;

interface StoreInterface extends ModelInterface
{
    public function getCreatedAt(): DateTimeInterface;

    public function getDeletedAt(): ?DateTimeInterface;

    public function getId(): string;

    public function getName(): string;

    public function getUpdatedAt(): DateTimeInterface;

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     created_at: string,
     *     updated_at: string,
     *     deleted_at?: string,
     * }
     */
    public function jsonSerialize(): array;
}
