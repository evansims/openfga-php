<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Store;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class GetStoreResponse implements GetStoreResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private Store $store,
    ) {
    }

    public function getStore(): Store
    {
        return $this->store;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['id'], $data['name'], $data['created_at'], $data['updated_at']));

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

        if (200 === $response->getStatusCode() && is_array($data)) {
            return new static(
                store: Store::fromArray($data),
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
