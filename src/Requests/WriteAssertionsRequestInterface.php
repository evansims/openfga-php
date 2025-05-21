<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{AssertionInterface, Collections\AssertionsInterface};

interface WriteAssertionsRequestInterface extends RequestInterface
{
    /**
     * @return AssertionsInterface<AssertionInterface>
     */
    public function getAssertions(): AssertionsInterface;

    public function getModel(): string;

    public function getStore(): string;
}
