<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Stores;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class ListStoresResponse extends Response
{
    public function __construct(
        public Stores $stores,
        public string $continuationToken,
    ) {
    }

    public function toArray(): array
    {
        return [
            'stores' => $this->stores->toArray(),
            'continuation_token' => $this->continuationToken,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            stores: Stores::fromArray($data['stores']),
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

        if ($response->getStatusCode() === 200) {
            return new static(
                stores: Stores::fromArray($data['stores']),
                continuationToken: $data['continuation_token'],
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
