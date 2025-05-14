<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class ListObjectsResponse implements ListObjectsResponseInterface
{
    use ResponseTrait;

    /**
     * @param array<int, string> $objects
     */
    public function __construct(
        private array $objects,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getObjects(): array
    {
        return $this->objects;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['objects']) && is_array($data['objects'])) {
            /** @var array<int, string> $objects */
            $objects = $data['objects'];

            return new self(
                objects: $objects,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
