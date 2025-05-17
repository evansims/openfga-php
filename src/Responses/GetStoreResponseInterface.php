<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use OpenFGA\Models\StoreInterface;
use OpenFGA\Schema\SchemaInterface;

interface GetStoreResponseInterface extends ResponseInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getDeletedAt(): ?DateTimeImmutable;

    public function getId(): string;

    public function getName(): string;

    public function getStore(): StoreInterface;

    public function getUpdatedAt(): DateTimeImmutable;

    public static function schema(): SchemaInterface;
}
