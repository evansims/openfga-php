<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Assertion, Assertions, AssertionsInterface, AuthorizationModelId, AuthorizationModelIdInterface};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;
use function is_string;

final class ReadAssertionsResponse implements ReadAssertionsResponseInterface
{
    use ResponseTrait;

    public function __construct(
        private ?AssertionsInterface $assertions,
        private AuthorizationModelIdInterface $authorizationModelId,
    ) {
    }

    public function getAssertions(): ?AssertionsInterface
    {
        return $this->assertions;
    }

    public function getAuthorizationModelId(): AuthorizationModelIdInterface
    {
        return $this->authorizationModelId;
    }

    public static function fromResponse(HttpResponseInterface $response): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data) && isset($data['authorization_model_id'])) {
            // @phpstan-ignore-next-line
            $authorizationModelId = new AuthorizationModelId(id: $data['authorization_model_id']);

            // @phpstan-ignore-next-line
            $assertions = isset($data['assertions']) ? Assertions::fromArray($data['assertions']) : null;

            return new self(
                assertions: $assertions,
                authorizationModelId: $authorizationModelId,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }
}
