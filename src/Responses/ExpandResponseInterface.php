<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersetTreeInterface;

interface ExpandResponseInterface extends ResponseInterface
{
    /**
     * @return null|UsersetTreeInterface
     */
    public function getTree(): ?UsersetTreeInterface;

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): static;
}
