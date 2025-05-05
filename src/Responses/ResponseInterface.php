<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface extends JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * @param array<string, mixed> $data
     * @return static
     */
    public static function fromArray(array $data): static;

    public static function fromResponse(HttpResponseInterface $response): static;
}
