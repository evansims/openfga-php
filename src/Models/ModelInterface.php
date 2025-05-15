<?php

declare(strict_types=1);

namespace OpenFGA\Models;

use JsonSerializable;
use OpenFGA\Schema\SchemaInterface;

interface ModelInterface extends JsonSerializable
{
    public static function Schema(): SchemaInterface;
}
