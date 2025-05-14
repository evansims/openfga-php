<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{UsersetTree, UsersetTreeInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ExpandResponse implements ExpandResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private ?UsersetTreeInterface $tree = null,
    ) {
    }

    public function getTree(): ?UsersetTreeInterface
    {
        return $this->tree;
    }

    public static function fromArray(array $data): static
    {
        if (isset($data['tree']) && is_array($data['tree'])) {
            return new self(
                tree: UsersetTree::fromArray($data['tree']),
            );
        }

        return new self();
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
            return self::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
