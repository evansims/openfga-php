<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\AuthorizationModel;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

final class GetAuthorizationModelResponse extends Response implements ResponseInterface
{
    public function __construct(
        public AuthorizationModel $authorizationModel,
    ) {
    }

    public function toArray(): array
    {
        return $this->authorizationModel->toArray();
    }

    public static function fromArray(array $data): static
    {
        return new self(
            authorizationModel: AuthorizationModel::fromArray($data),
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

        if (200 === $response->getStatusCode() && isset($data['authorization_model'])) {
            return new static(
                authorizationModel: AuthorizationModel::fromArray($data['authorization_model']),
            );
        }

        Response::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
