<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use JsonSerializable;

interface ResponseInterface extends JsonSerializable
{
    public function toArray(): array;

    public function jsonSerialize(): array;

    public static function fromArray(array $data): static;

    public static function fromResponse(HttpResponseInterface $response): static;
}
