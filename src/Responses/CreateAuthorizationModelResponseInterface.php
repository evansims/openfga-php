<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaInterface;

interface CreateAuthorizationModelResponseInterface extends ResponseInterface
{
    public function getModel(): string;

    public static function schema(): SchemaInterface;
}
