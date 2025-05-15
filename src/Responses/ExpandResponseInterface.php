<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersetTreeInterface;
use OpenFGA\Schema\SchemaInterface;

interface ExpandResponseInterface extends ResponseInterface
{
    public function getTree(): ?UsersetTreeInterface;

    public static function Schema(): SchemaInterface;
}
