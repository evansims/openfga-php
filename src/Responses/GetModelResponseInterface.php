<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;

interface GetModelResponseInterface extends ResponseInterface
{
    /**
     * @return null|AuthorizationModelInterface
     */
    public function getAuthorizationModel(): ?AuthorizationModelInterface;

    /**
     * @param array<string, null|string> $data
     */
    public static function fromArray(array $data): static;
}
