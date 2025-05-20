<?php

declare(strict_types=1);

namespace OpenFGA\Models\Enums;

enum TupleOperation: string
{
    case TUPLE_OPERATION_DELETE = 'TUPLE_OPERATION_DELETE';

    case TUPLE_OPERATION_WRITE = 'TUPLE_OPERATION_WRITE';
}
