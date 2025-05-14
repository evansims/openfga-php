<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModel;

interface GetModelResponseInterface extends ResponseInterface
{
    /**
     * @return null|AuthorizationModel
     */
    public function getAuthorizationModel(): ?AuthorizationModel;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
