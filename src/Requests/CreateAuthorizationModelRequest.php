<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use InvalidArgumentException;
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
        if ('' === $this->store) {
            throw new InvalidArgumentException('Store ID cannot be empty');
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $conditions = null;

        if ($this->getConditions() instanceof ConditionsInterface && $this->getConditions()->count() > 0) {
            $conditions = $this->getConditions()->jsonSerialize();
        }

        $body = array_filter([
            'schema_version' => $this->getSchemaVersion()->value,
            'type_definitions' => $this->getTypeDefinitions()->jsonSerialize(),
            'conditions' => $conditions,
        ], static fn ($value): bool => null !== $value);

        $stream = $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));

        return new RequestContext(
            method: RequestMethod::POST,
            url: '/stores/' . $this->store . '/authorization-models',
            body: $stream,
        );
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getStore(): string
    {
        return $this->store;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }
}
