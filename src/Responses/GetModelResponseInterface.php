<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModel;

interface GetModelResponseInterface extends ResponseInterface
{
    /**
     * @return AuthorizationModel|null
     */
    public function getAuthorizationModel(): ?AuthorizationModel;

    /**
     * @param array<string, string|null> $data
     */
    public static function fromArray(array $data): static;
}
