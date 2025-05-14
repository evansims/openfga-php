<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\{AuthorizationModelsInterface, ContinuationTokenInterface};

interface ListModelsResponseInterface extends ResponseInterface
{
    /**
     * @return AuthorizationModelsInterface
     */
    public function getAuthorizationModels(): AuthorizationModelsInterface;

    /**
     * @return ?ContinuationTokenInterface
     */
    public function getContinuationToken(): ?ContinuationTokenInterface;
}
