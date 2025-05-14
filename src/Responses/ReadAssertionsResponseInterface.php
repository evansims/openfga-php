<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AssertionsInterface;

interface ReadAssertionsResponseInterface extends ResponseInterface
{
    public function getAssertions(): ?AssertionsInterface;

    public function getAuthorizationModelId(): string;

    /**
     * @param array<string> $data
     */
    public static function fromArray(array $data): static;
}
