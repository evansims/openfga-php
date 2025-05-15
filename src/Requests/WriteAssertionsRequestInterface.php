<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\AssertionsInterface;
use OpenFGA\Options\WriteAssertionsOptionsInterface;

interface WriteAssertionsRequestInterface extends RequestInterface
{
    public function getAssertions(): AssertionsInterface;

    public function getAuthorizationModel(): string;

    public function getOptions(): ?WriteAssertionsOptionsInterface;

    public function getStore(): string;
}
