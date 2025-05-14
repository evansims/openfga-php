<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Assertions, AssertionsInterface, AuthorizationModelId, AuthorizationModelIdInterface};
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

        if (200 === $response->getStatusCode() && is_array($data)) {
            [$authorizationModelId, $assertions] = self::validatedReadAssertionsResponseShape($data);

            return new self(
                assertions: $assertions,
                authorizationModelId: $authorizationModelId,
            );
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    /**
     * @param array<mixed> $data
     *
     * @return array{AuthorizationModelIdInterface, null|AssertionsInterface}
     */
    public static function validatedReadAssertionsResponseShape(array $data): array
    {
        $assertions = null;
        $authorizationModelId = null;

        if (! isset($data['authorization_model_id']) || ! is_string($data['authorization_model_id'])) {
            throw new ApiUnexpectedResponseException('Missing authorization_model_id');
        }

        $authorizationModelId = new AuthorizationModelId(id: $data['authorization_model_id']);

        if (isset($data['assertions'])) {
            $assertions = Assertions::fromArray($data['assertions']);
        }

        return [$authorizationModelId, $assertions];
    }
}
