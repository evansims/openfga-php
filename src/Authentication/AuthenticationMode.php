<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

enum AuthenticationMode
{
    case NONE;
    case CLIENT_CREDENTIALS;
    case TOKEN;
}
