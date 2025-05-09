<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class ListObjectsResponse extends Response
{
    public function __construct(
        public array $objects,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'objects' => $this->objects,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        assert(isset($data['objects']) && is_array($data['objects']));

        return new self(
            objects: $data['objects'],
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

        if (200 === $response->getStatusCode() && isset($data['objects']) && is_array($data['objects'])) {
            return static::fromArray($data);
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
