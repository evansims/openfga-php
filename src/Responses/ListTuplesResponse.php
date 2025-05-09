<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Tuples, TuplesInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ListTuplesResponse extends Response
{
    public function __construct(
        public TuplesInterface $tuples,
        public string $continuationToken,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tuples' => $this->tuples,
            'continuation_token' => $this->continuationToken,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        assert(isset($data['tuples']) && is_array($data['tuples']) && isset($data['continuation_token']) && is_string($data['continuation_token']));

        return new self(
            tuples: Tuples::fromArray($data['tuples']),
            continuationToken: $data['continuation_token'],
        );
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode()) {
            return static::fromArray($data);
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
