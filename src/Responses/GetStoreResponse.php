<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Store;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class GetStoreResponse extends Response implements ResponseInterface
{
    public function __construct(
        public Store $store,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        /** @var array<string, mixed> */
        return $this->store->toArray();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            store: Store::fromArray($data),
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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['store']) && is_array($data['store'])) {
            return new static(
                store: Store::fromArray($data['store']),
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
