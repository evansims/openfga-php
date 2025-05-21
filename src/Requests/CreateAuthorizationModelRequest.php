<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\{ConditionInterface, TypeDefinitionInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Network\{RequestContext, RequestMethod};
use Override;
use Psr\Http\Message\StreamFactoryInterface;

final class CreateAuthorizationModelRequest implements CreateAuthorizationModelRequestInterface
{
    /**
     * @param string                                            $store
     * @param TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions
     * @param SchemaVersion                                     $schemaVersion
     * @param ?ConditionsInterface<ConditionInterface>          $conditions
     */
    public function __construct(
        private string $store,
        private TypeDefinitionsInterface $typeDefinitions,
        private SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        private ?ConditionsInterface $conditions = null,
    ) {
    }

    #[Override]
    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $body = array_filter([
            'type_definitions' => $this->getTypeDefinitions()->jsonSerialize(),
            'schema_version' => (string) $this->getSchemaVersion()->value,
            'conditions' => $this->getConditions()?->jsonSerialize(),
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/authorization-models',
            body: $stream,
        );
    }

    #[Override]
    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    #[Override]
    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }
}
