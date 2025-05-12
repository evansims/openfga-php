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

final class ListTuplesResponse implements ListTuplesResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private TuplesInterface $tuples,
        private string $continuationToken,
    ) {
    }

    public function getContinuationToken(): string
    {
        return $this->continuationToken;
    }

    public function getTuples(): TuplesInterface
    {
        return $this->tuples;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['tuples']) && is_array($data['tuples']));
        assert(isset($data['continuation_token']) && is_string($data['continuation_token']));

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

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
