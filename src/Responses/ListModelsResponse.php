<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{AuthorizationModels, AuthorizationModelsInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function assert;
use function is_array;

final class ListModelsResponse implements ListModelsResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private AuthorizationModelsInterface $authorizationModels,
        private ?string $continuationToken = null,
    ) {
    }

    public function getAuthorizationModels(): AuthorizationModelsInterface
    {
        return $this->authorizationModels;
    }

    public function getContinuationToken(): ?string
    {
        return $this->continuationToken;
    }

    public static function fromArray(array $data): static
    {
        assert(isset($data['authorization_models']) && is_array($data['authorization_models']));

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

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['authorization_models']) && is_array($data['authorization_models'])) {
            return new static(
                authorizationModels: AuthorizationModels::fromArray($data['authorization_models']),
                continuationToken: $data['continuation_token'] ?? null,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
