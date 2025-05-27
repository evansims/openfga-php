<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

enum SchemaVersion: string
{
    case V1_0 = '1.0';
    case V1_1 = '1.1';
}
