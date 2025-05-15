<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Network\RequestContext;
use Psr\Http\Message\StreamFactoryInterface;

interface RequestInterface
{
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext;
}
