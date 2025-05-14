<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\AuthorizationModel;
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

final class GetModelResponse implements GetModelResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private ?AuthorizationModel $authorizationModel = null,
    ) {
    }

    public function getAuthorizationModel(): ?AuthorizationModel
    {
        return $this->authorizationModel;
    }

    public static function fromArray(array $data): static
    {
        if (isset($data['authorization_model']) && is_array($data['authorization_model'])) {
            return new self(
                authorizationModel: AuthorizationModel::fromArray($data['authorization_model']),
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
