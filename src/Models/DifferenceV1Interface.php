<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface DifferenceV1Interface extends ModelInterface
{
    public function getBase(): UsersetInterface;

    public function getSubtract(): UsersetInterface;
}
