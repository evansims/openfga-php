<?php

declare(strict_types=1);

namespace OpenFGA\Language;

use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Schema\SchemaValidator;

interface DslTransformerInterface
{
    public static function fromDsl(string $dsl, SchemaValidator $validator): AuthorizationModelInterface;

    public static function toDsl(AuthorizationModelInterface $model): string;
}
