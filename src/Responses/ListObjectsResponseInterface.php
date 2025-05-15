<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaInterface;

interface ListObjectsResponseInterface extends ResponseInterface
{
    /**
     * @return array<int, string>
     */
    public function getObjects(): array;

    public static function Schema(): SchemaInterface;
}
