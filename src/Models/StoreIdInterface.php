<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type StoreIdShape = string
 */
interface StoreIdInterface extends ModelInterface
{
    public function __toString(): string;

    public function getId(): string;

    public function jsonSerialize(): string;

    public static function fromStore(Store $store): self;
}
