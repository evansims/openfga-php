<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;
use function is_string;

final class CreateAuthorizationModelResponse extends Response
{
    public function __construct(
        public string $authorizationModelId,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
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
        $authorizationModelId = isset($data['authorization_model_id']) && is_string($data['authorization_model_id'])
            ? $data['authorization_model_id']
            : '';

        return new self(
            authorizationModelId: $authorizationModelId,
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

        if (201 === $response->getStatusCode() && is_array($data) && isset($data['authorization_model_id']) && is_string($data['authorization_model_id'])) {
            return new static(
                authorizationModelId: $data['authorization_model_id'],
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
