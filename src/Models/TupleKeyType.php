<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum TupleKeyType
{
    case ASSERTION_TUPLE_KEY;

    case CONTEXTUAL_TUPLE_KEY;

    case GENERIC_TUPLE_KEY;
}
