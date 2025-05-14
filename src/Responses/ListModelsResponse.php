<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{AuthorizationModels, AuthorizationModelsInterface, ContinuationToken, ContinuationTokenInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class ListModelsResponse implements ListModelsResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private AuthorizationModelsInterface $authorizationModels,
        private ?ContinuationTokenInterface $continuationToken = null,
    ) {
    }

    public function getAuthorizationModels(): AuthorizationModelsInterface
    {
        return $this->authorizationModels;
    }

    public function getContinuationToken(): ?ContinuationTokenInterface
    {
        return $this->continuationToken;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['authorization_models'])) {
            // @phpstan-ignore-next-line
            $authorizationModels = AuthorizationModels::fromArray($data['authorization_models']);

            // @phpstan-ignore-next-line
            $continuationToken = $data['continuation_token'] ? new ContinuationToken($data['continuation_token']) : null;

            return new static(
                authorizationModels: $authorizationModels,
                continuationToken: $continuationToken,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
