<?php

declare(strict_types=1);

namespace OpenFGA\Models;

enum TypeName: string
{
    case UNSPECIFIED = 'TYPE_NAME_UNSPECIFIED';
    case ANY = 'TYPE_NAME_ANY';
    case BOOL = 'TYPE_NAME_BOOL';
    case STRING = 'TYPE_NAME_STRING';
    case INT = 'TYPE_NAME_INT';
    case UINT = 'TYPE_NAME_UINT';
    case DOUBLE = 'TYPE_NAME_DOUBLE';
    case DURATION = 'TYPE_NAME_DURATION';
    case TIMESTAMP = 'TYPE_NAME_TIMESTAMP';
    case MAP = 'TYPE_NAME_MAP';
    case LIST = 'TYPE_NAME_LIST';
    case IPADDRESS = 'TYPE_NAME_IPADDRESS';
}
