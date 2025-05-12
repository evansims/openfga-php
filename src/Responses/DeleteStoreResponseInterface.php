<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface DeleteStoreResponseInterface extends ResponseInterface
{
    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): static;
}
