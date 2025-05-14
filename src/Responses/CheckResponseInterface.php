<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

interface CheckResponseInterface extends ResponseInterface
{
    /**
     * @return null|bool
     */
    public function getAllowed(): ?bool;

    /**
     * @return null|string
     */
    public function getResolution(): ?string;

    /**
     * @param array<string, null|bool|string> $data
     */
    public static function fromArray(array $data): static;
}
