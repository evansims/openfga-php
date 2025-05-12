<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use DateTimeImmutable;

interface CreateStoreResponseInterface extends ResponseInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable;

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable;

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): static;
}
