<?php

declare(strict_types=1);

namespace OpenFGA\Responses;

use Exception;
use OpenFGA\Exceptions\ApiUnexpectedResponseException;
use OpenFGA\Models\{Assertion, Assertions, AssertionsInterface};
use OpenFGA\Schema\{Schema, SchemaInterface, SchemaProperty, SchemaValidator};
use Psr\Http\Message\ResponseInterface as HttpResponseInterface;

use function is_array;

/**
 * @implements ReadAssertionsResponseInterface<array{authorization_model_id: string, assertions: array<array{tuple_key: array{user: string, relation: string, object: string}, expectation: bool}>}>
 */
final class ReadAssertionsResponse implements ReadAssertionsResponseInterface
{
    use ResponseTrait;

    private static ?SchemaInterface $schema = null;

    /**
     * @param AssertionsInterface<Assertion> $assertions
     * @param string                         $authorizationModelId
     */
    public function __construct(
        private ?AssertionsInterface $assertions,
        private string $authorizationModelId,
    ) {
    }

    /**
     * @return null|AssertionsInterface<Assertion>
     */
    public function getAssertions(): ?AssertionsInterface
    {
        return $this->assertions;
    }

    public function getAuthorizationModelId(): string
    {
        return $this->authorizationModelId;
    }

    /**
     * @return array{authorization_model_id: string, assertions: list<array{tuple_key: array{user: string, relation: string, object: string}, expectation: bool}>}
     */
    public function toArray(): array
    {
        $result = [
            'authorization_model_id' => $this->authorizationModelId,
            'assertions' => [],
        ];

        if (null === $this->assertions) {
            return $result;
        }

        $assertions = [];

        foreach ($this->assertions as $assertion) {
            if (! $assertion instanceof Assertion) {
                continue;
            }

            $tupleKey = $assertion->getTupleKey();

            $assertions[] = [
                'tuple_key' => [
                    'user' => $tupleKey->getUser(),
                    'relation' => $tupleKey->getRelation(),
                    'object' => $tupleKey->getObject(),
                ],
                'expectation' => $assertion->getExpectation(),
            ];
        }

        $result['assertions'] = $assertions;

        return $result;
    }

    public static function fromResponse(HttpResponseInterface $response, SchemaValidator $validator): static
    {
        $json = (string) $response->getBody();

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new ApiUnexpectedResponseException($e->getMessage());
        }

        if (200 === $response->getStatusCode() && is_array($data)) {
            $validator->registerSchema(Assertions::schema());
            $validator->registerSchema(self::schema());

            return $validator->validateAndTransform($data, self::class);
        }

        self::handleResponseException($response);

        throw new ApiUnexpectedResponseException($json);
    }

    public static function schema(): SchemaInterface
    {
        return self::$schema ??= new Schema(
            className: self::class,
            properties: [
                new SchemaProperty(name: 'assertions', type: Assertions::class, required: false),
                new SchemaProperty(name: 'authorization_model_id', type: 'string', required: true),
            ],
        );
    }
}
