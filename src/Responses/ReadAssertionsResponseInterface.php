<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AssertionsInterface;
use OpenFGA\Schema\SchemaInterface;

interface ReadAssertionsResponseInterface extends ResponseInterface
{
    public function getAssertions(): ?AssertionsInterface;

    public function getAuthorizationModelId(): string;

    public static function schema(): SchemaInterface;
}
