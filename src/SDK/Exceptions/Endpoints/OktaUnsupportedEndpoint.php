<?php
namespace OpenFGA\SDK\Exceptions\Endpoints;

use Psr\Container\ContainerExceptionInterface;

final class OktaUnsupportedEndpoint extends ContainerExceptionInterface
{
    public const string EXCEPTION_MESSAGE = 'This endpoint is currently unsupported with Okta FGA';

    public function __construct()
    {
        parent::__construct(self::EXCEPTION_MESSAGE);
    }
}
