<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Stores;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ListStoresResponse extends Response
{
    public function __construct(
        public Stores $stores,
        public ?string $continuationToken = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'stores' => $this->stores->toArray(),
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
        assert(isset($data['stores']) && is_array($data['stores']));

        $continuationToken = isset($data['continuation_token']) && is_string($data['continuation_token'])
            ? $data['continuation_token']
            : null;

        return new self(
            stores: Stores::fromArray($data['stores']),
            continuationToken: $continuationToken,
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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['stores']) && is_array($data['stores'])) {
            $continuationToken = isset($data['continuation_token']) && is_string($data['continuation_token'])
                ? $data['continuation_token']
                : null;

            return new static(
                stores: Stores::fromArray($data['stores']),
                continuationToken: $continuationToken,
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
