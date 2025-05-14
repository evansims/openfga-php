<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{ContinuationToken, ContinuationTokenInterface, Tuples, TuplesInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ListTuplesResponse implements ListTuplesResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private TuplesInterface $tuples,
        private ?ContinuationTokenInterface $continuationToken = null,
    ) {
    }

    public function getContinuationToken(): ?ContinuationTokenInterface
    {
        return $this->continuationToken;
    }

    public function getTuples(): TuplesInterface
    {
        return $this->tuples;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['tuples']) && isset($data['continuation_token'])) {
            // @phpstan-ignore-next-line
            $tuples = Tuples::fromArray($data['tuples']);

            // @phpstan-ignore-next-line
            $continuationToken = $data['continuation_token'] ? new ContinuationToken($data['continuation_token']) : null;

            return new self(
                tuples: $tuples,
                continuationToken: $continuationToken,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
