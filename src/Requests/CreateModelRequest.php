<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\Models\{ConditionsInterface, SchemaVersion, StoreIdInterface, TypeDefinitionsInterface};
use OpenFGA\RequestOptions\CreateModelOptions;

final class CreateModelRequest
{
    public function __construct(
        private RequestFactory $requestFactory,
        private StoreIdInterface $storeId,
        private TypeDefinitionsInterface $typeDefinitions,
        private SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        private ?ConditionsInterface $conditions = null,
        private ?CreateModelOptions $options = null,
    ) {
    }

    public function getConditions(): ?ConditionsInterface
    {
        return $this->conditions;
    }

    public function getOptions(): ?CreateModelOptions
    {
        return $this->options;
    }

    public function getSchemaVersion(): SchemaVersion
    {
        return $this->schemaVersion;
    }

    public function getStoreId(): StoreIdInterface
    {
        return $this->storeId;
    }

    public function getTypeDefinitions(): TypeDefinitionsInterface
    {
        return $this->typeDefinitions;
    }

    public function toJson(): string
    {
        $body = [];

        $body['type_definitions'] = $this->getTypeDefinitions()->toArray();
        $body['schema_version'] = (string) $this->getSchemaVersion();

        if (null !== $this->getConditions()) {
            $body['conditions'] = $this->getConditions()->toArray();
        }

        return json_encode($body, JSON_THROW_ON_ERROR);
    }

    public function toRequest(): Request
    {
        $body = $this->requestFactory->getHttpStreamFactory()->createStream($this->toJson());

        return $this->requestFactory->post(
            url: $this->requestFactory->getEndpointUrl('/stores/' . $this->getStoreId() . '/authorization-models'),
            options: $this->getOptions(),
            body: $body,
            headers: $this->requestFactory->getEndpointHeaders(),
        );
    }
}
