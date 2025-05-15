<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaInterface;

interface CreateAuthorizationModelResponseInterface extends ResponseInterface
{
    public function getAuthorizationModelId(): string;

    public static function Schema(): SchemaInterface;
}
