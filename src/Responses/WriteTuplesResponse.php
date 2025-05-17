<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Schema\SchemaValidator;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

/**
 * @implements WriteTuplesResponseInterface<array>
 */
final class WriteTuplesResponse implements WriteTuplesResponseInterface
{
    use ResponseTrait;

    public function __construct()
    {
    }

    /**
     * @return array<never, never>
     */
    public function toArray(): array
    {
        return [];
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        if (204 === $response->getStatusCode()) {
            return new self();
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException('');
    }
}
