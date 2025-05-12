<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModels;

interface ListModelsResponseInterface extends ResponseInterface
{
    /**
     * @return AuthorizationModels
     */
    public function getAuthorizationModels(): AuthorizationModels;

    /**
     * @return ?string
     */
    public function getContinuationToken(): ?string;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
