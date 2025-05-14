<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelsInterface;

interface ListModelsResponseInterface extends ResponseInterface
{
    /**
     * @return AuthorizationModelsInterface
     */
    public function getAuthorizationModels(): AuthorizationModelsInterface;

    /**
     * @return ?string
     */
    public function getContinuationToken(): ?string;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
