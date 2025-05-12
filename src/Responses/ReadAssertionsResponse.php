<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\Assertions;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ReadAssertionsResponse implements ReadAssertionsResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private ?Assertions $assertions,
        private string $authorizationModelId,
    ) {
    }

    public function getAssertions(): ?Assertions
    {
        return $this->assertions;
    }

    public function getAuthorizationModelId(): string
    {
        return $this->authorizationModelId;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['authorization_model_id']) && is_string($data['authorization_model_id']));

        return new self(
            assertions: isset($data['assertions']) && is_array($data['assertions']) ? Assertions::fromArray($data['assertions']) : null,
            authorizationModelId: $data['authorization_model_id'],
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
            return static::fromArray($data);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
