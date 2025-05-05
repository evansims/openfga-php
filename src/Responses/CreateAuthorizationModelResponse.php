<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;

final class CreateAuthorizationModelResponse extends Response
{
    public function __construct(
        public string $authorizationModelId,
    ) {
    }

    public function toArray(): array
    {
        return [
            'authorization_model_id' => $this->authorizationModelId,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
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

        if ($response->getStatusCode() === 201) {
            return new static(
                authorizationModelId: $data['authorization_model_id'],
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
