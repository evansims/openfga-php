<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\AssertionInterface;
use OpenFGA\Models\Collections\AssertionsInterface;
use OpenFGA\Schema\SchemaInterface;

interface ReadAssertionsResponseInterface extends ResponseInterface
{
    /**
     * @return null|AssertionsInterface<AssertionInterface>
     */
    public function getAssertions(): ?AssertionsInterface;

    public function getModel(): string;

    public static function schema(): SchemaInterface;
}
