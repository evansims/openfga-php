<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Schema\SchemaInterface;

interface GetAuthorizationModelResponseInterface extends ResponseInterface
{
    public function getAuthorizationModel(): ?AuthorizationModelInterface;

    public static function schema(): SchemaInterface;
}
