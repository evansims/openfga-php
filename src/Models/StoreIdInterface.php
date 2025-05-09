<?php

declare(strict_types=1);

namespace OpenFGA\Models;

interface StoreIdInterface extends ModelInterface
{
    public function __toString(): string;

    public static function fromStore(Store $store): self;
}
