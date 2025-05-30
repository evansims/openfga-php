<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use const JSON_THROW_ON_ERROR;

use InvalidArgumentException;
use JsonException;
use OpenFGA\Exceptions\{ClientError};
use OpenFGA\Exceptions\ClientThrowable;
use OpenFGA\Messages;
use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\{ConditionInterface, TypeDefinitionInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Translation\Translator;
use Override;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionException;

/**
 * Request for creating a new authorization model in OpenFGA.
 *
 * Authorization models define the permission structure for your application,
 * including object types, relationships, and how permissions are computed.
 * Models are immutable once created and identified by a unique ID.
 *
 * @see CreateAuthorizationModelRequestInterface For the complete API specification
 * @see https://openfga.dev/docs/api#/Authorization%20Models/WriteAuthorizationModel Authorization model creation API endpoint
 */
final readonly class CreateAuthorizationModelRequest implements CreateAuthorizationModelRequestInterface
{
    /**
     * @param string                                            $store           The store ID
     * @param TypeDefinitionsInterface<TypeDefinitionInterface> $typeDefinitions Type definitions
     * @param SchemaVersion                                     $schemaVersion   Schema version
     * @param ?ConditionsInterface<ConditionInterface>          $conditions      Conditions (optional)
     *
     * @throws ClientThrowable          If the store ID is empty
     * @throws InvalidArgumentException If message translation parameters are invalid
     * @throws ReflectionException      If exception location capture fails
     */
    public function __construct(
        private string $store,
        private TypeDefinitionsInterface $typeDefinitions,
        private SchemaVersion $schemaVersion = SchemaVersion::V1_1,
        private ?ConditionsInterface $conditions = null,
    ) {
        if ('' === $this->store) {
            throw ClientError::Validation->exception(context: ['message' => Translator::trans(Messages::REQUEST_STORE_ID_EMPTY)]);
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
     *
     * @throws JsonException
     */
    #[Override]
    public function getRequest(StreamFactoryInterface $streamFactory): RequestContext
    {
        $conditions = null;
        $conditionsCollection = $this->getConditions();

        if ($conditionsCollection instanceof ConditionsInterface && 0 < $conditionsCollection->count()) {
            $conditions = $conditionsCollection->jsonSerialize();
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
