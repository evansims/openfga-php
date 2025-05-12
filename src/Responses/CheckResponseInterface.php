<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface CheckResponseInterface extends ResponseInterface
{
    /**
     * @return bool|null
     */
    public function getAllowed(): ?bool;

    /**
     * @return string|null
     */
    public function getResolution(): ?string;

    /**
     * @param array<string, bool|string|null> $data
     */
    public static function fromArray(array $data): static;
}
