<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

interface ResponseInterface extends JsonSerializable
{
    public function jsonSerialize(): array;

    public function toArray(): array;

    public static function fromArray(array $data): static;

    public static function fromResponse(HttpResponseInterface $response): static;
}
