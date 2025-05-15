<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{ConditionsInterface, SchemaVersion, TypeDefinitionsInterface};
use OpenFGA\Network\{NetworkRequestMethod, RequestContext};
use OpenFGA\Options\CreateAuthorizationModelOptionsInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class CreateAuthorizationModelRequest implements CreateAuthorizationModelRequestInterface
{
    public function __construct(
        private string $store,
        private TypeDefinitionsInterface $typeDefinitions,
        private SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        private ?ConditionsInterface $conditions = null,
        private ?CreateAuthorizationModelOptionsInterface $options = null,
    ) {
    }

    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    public function getOptions(): ?CreateAuthorizationModelOptionsInterface
    {
        return $this->options;
    }

    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = [
            'type_definitions' => $this->getTypeDefinitions()->jsonSerialize(),
            'schema_version' => (string) $this->getSchemaVersion()->value,
        ];

        if (null !== $this->getConditions()) {
            $body['conditions'] = $this->getConditions()->jsonSerialize();
        }

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: NetworkRequestMethod::POST,
            url: '/stores/' . $this->getStore() . '/authorization-models',
            body: $stream,
        );
    }

    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    public function getStore(): string
    {
        return $this->store;
    }

    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }
}
