<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Schema\SchemaInterface;

interface CheckResponseInterface extends ResponseInterface
{
    public function getAllowed(): ?bool;

    public function getResolution(): ?string;

    public static function schema(): SchemaInterface;
}
