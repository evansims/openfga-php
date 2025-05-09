<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum ConsistencyPreference: string
{
    case HIGHER_CONSISTENCY = 'HIGHER_CONSISTENCY';

    case MINIMIZE_LATENCY = 'MINIMIZE_LATENCY';

    case UNSPECIFIED = 'UNSPECIFIED';
}
