<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\Requests\RequestFactory;

interface ClientInterface
{
    public function getAuthentication(): AuthenticationInterface;

    public function getConfiguration(): ConfigurationInterface;

    public function getRequestFactory(): RequestFactory;
}
