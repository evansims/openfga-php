<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use DateTimeImmutable;

interface StoreInterface extends ModelInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getDeletedAt(): ?DateTimeImmutable;

    public function getId(): string;

    public function getName(): string;

    public function getUpdatedAt(): DateTimeImmutable;
}
