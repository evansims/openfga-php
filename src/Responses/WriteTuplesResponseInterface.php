<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface WriteTuplesResponseInterface extends ResponseInterface
{
    /**
     * @param array<string> $data
     */
    public static function fromArray(array $data): static;
}
