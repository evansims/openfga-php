<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\AssertionsInterface;

interface WriteAssertionsRequestInterface extends RequestInterface
{
    public function getAssertions(): AssertionsInterface;

    public function getAuthorizationModel(): string;

    public function getStore(): string;
}
