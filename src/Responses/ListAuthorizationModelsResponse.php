<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\AuthorizationModels;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class ListAuthorizationModelsResponse extends Response
{
    public function __construct(
        public AuthorizationModels $authorizationModels,
        public ?string $continuationToken = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'authorization_models' => $this->authorizationModels->toArray(),
            'continuation_token' => $this->continuationToken,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            authorizationModels: AuthorizationModels::fromArray($data['authorization_models']),
            continuationToken: $data['continuation_token'] ?? null,
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

        if (200 === $response->getStatusCode()) {
            return new static(
                authorizationModels: AuthorizationModels::fromArray($data['authorization_models']),
                continuationToken: $data['continuation_token'] ?? null,
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
