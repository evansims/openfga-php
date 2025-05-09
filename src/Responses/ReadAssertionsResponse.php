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

final class ReadAssertionsResponse extends Response
{
    public function __construct(
        public Assertions $assertions,
        public string $authorizationModelId,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'assertions' => $this->assertions->toArray(),
            'authorization_model_id' => $this->authorizationModelId,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        assert(isset($data['assertions']) && is_array($data['assertions']));
        assert(isset($data['authorization_model_id']) && is_string($data['authorization_model_id']));

        return new self(
            assertions: Assertions::fromArray($data['assertions']),
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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['assertions']) && is_array($data['assertions']) && isset($data['authorization_model_id']) && is_string($data['authorization_model_id'])) {
            return static::fromArray($data);
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
