<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\AuthorizationModels;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;
use function is_string;

final class ListAuthorizationModelsResponse extends Response
{
    public function __construct(
        public AuthorizationModels $authorizationModels,
        public ?string $continuationToken = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'authorization_models' => $this->authorizationModels->toArray(),
            'continuation_token' => $this->continuationToken,
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        assert(isset($data['authorization_models']) && is_array($data['authorization_models']));

        $continuationToken = isset($data['continuation_token']) && is_string($data['continuation_token'])
            ? $data['continuation_token']
            : null;

        return new self(
            authorizationModels: AuthorizationModels::fromArray($data['authorization_models']),
            continuationToken: $continuationToken,
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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['authorization_models']) && is_array($data['authorization_models'])) {
            return new static(
                authorizationModels: AuthorizationModels::fromArray($data['authorization_models']),
                continuationToken: $data['continuation_token'] ?? null,
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
