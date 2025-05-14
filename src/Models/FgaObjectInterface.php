<?php

declare(strict_types=1);

namespace OpenFGA\Models;

/**
 * @psalm-type FgaObjectShape = array{id: string}
 */
interface FgaObjectInterface extends ModelInterface
{
    public function getId(): string;

    /**
     * @return FgaObjectShape
     */
    public function jsonSerialize(): array;

    /**
     * @param FgaObjectShape $data
     */
    public static function fromArray(array $data): self;
}
