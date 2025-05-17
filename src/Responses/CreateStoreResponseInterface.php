<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;
use OpenFGA\Schema\SchemaInterface;

interface CreateStoreResponseInterface extends ResponseInterface
{
    public function getCreatedAt(): DateTimeImmutable;

    public function getId(): string;

    public function getName(): string;

    public function getUpdatedAt(): DateTimeImmutable;

    public static function schema(): SchemaInterface;
}
