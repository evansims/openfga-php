<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface ListObjectsResponseInterface extends ResponseInterface
{
    /**
     * @return array<int, string>
     */
    public function getObjects(): array;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
