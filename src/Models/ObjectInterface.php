<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface ObjectInterface extends ModelInterface
{
    public function getId(): string;
}
