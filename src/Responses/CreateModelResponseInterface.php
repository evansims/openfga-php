<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface CreateModelResponseInterface extends ResponseInterface
{
    /**
     * @return string
     */
    public function getAuthorizationModelId(): string;

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): static;
}
