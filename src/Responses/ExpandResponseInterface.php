<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Models\UsersetTree;

interface ExpandResponseInterface extends ResponseInterface
{
    /**
     * @return null|UsersetTree
     */
    public function getTree(): ?UsersetTree;

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): static;
}
