<?php

declare(strict_types=1);

namespace OpenFGA\Network;

enum RequestMethod: string
{
    case DELETE = 'DELETE';

    case GET = 'GET';

    case POST = 'POST';

    case PUT = 'PUT';
}
