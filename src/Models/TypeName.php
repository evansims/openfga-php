<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum TypeName: string
{
    case ANY = 'TYPE_NAME_ANY';

    case BOOL = 'TYPE_NAME_BOOL';

    case DOUBLE = 'TYPE_NAME_DOUBLE';

    case DURATION = 'TYPE_NAME_DURATION';

    case INT = 'TYPE_NAME_INT';

    case IPADDRESS = 'TYPE_NAME_IPADDRESS';

    case LIST = 'TYPE_NAME_LIST';

    case MAP = 'TYPE_NAME_MAP';

    case STRING = 'TYPE_NAME_STRING';

    case TIMESTAMP = 'TYPE_NAME_TIMESTAMP';

    case UINT = 'TYPE_NAME_UINT';

    case UNSPECIFIED = 'TYPE_NAME_UNSPECIFIED';
}
