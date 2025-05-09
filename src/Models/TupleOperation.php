<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum TupleOperation: string
{
    case TUPLE_OPERATION_WRITE = 'TUPLE_OPERATION_WRITE';

    case TUPLE_OPERATION_DELETE = 'TUPLE_OPERATION_DELETE';
}
