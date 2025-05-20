<?php

declare(strict_types=1);

namespace OpenFGA\Authentication;

enum AuthenticationMode
{
    case CLIENT_CREDENTIALS;

    case NONE;

    case TOKEN;
}
