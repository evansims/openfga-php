<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Stores, StoresInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ListStoresResponse implements ListStoresResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private StoresInterface $stores,
        private string $continuationToken,
    ) {
    }

    public function getContinuationToken(): string
    {
        return $this->continuationToken;
    }

    public function getStores(): StoresInterface
    {
        return $this->stores;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['stores']) && isset($data['continuation_token'])) {
            // @phpstan-ignore-next-line
            $stores = Stores::fromArray($data['stores']);

            // @phpstan-ignore-next-line
            $continuationToken = (string) $data['continuation_token'];

            return new self(
                stores: $stores,
                continuationToken: $continuationToken,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
