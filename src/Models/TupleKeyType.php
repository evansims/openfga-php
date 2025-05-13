<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum TupleKeyType
{
    case GENERIC_TUPLE_KEY;
    case ASSERTION_TUPLE_KEY;
    case CONTEXTUAL_TUPLE_KEY;
}
