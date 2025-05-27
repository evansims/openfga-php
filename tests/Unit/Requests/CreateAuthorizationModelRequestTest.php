<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\CreateAuthorizationModelRequest;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

it('can be instantiated with required parameters', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions
    );

    expect($request)->toBeInstanceOf(CreateAuthorizationModelRequest::class);
    expect($request->getStore())->toBe('test-store');
    expect($request->getTypeDefinitions())->toBe($typeDefinitions);
    expect($request->getSchemaVersion())->toBe(SchemaVersion::V1_1); // Default value
    expect($request->getConditions())->toBeNull();
});

it('can be instantiated with all parameters', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);
    $conditions = Mockery::mock(ConditionsInterface::class);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_0,
        conditions: $conditions
    );

    expect($request->getStore())->toBe('test-store');
    expect($request->getTypeDefinitions())->toBe($typeDefinitions);
    expect($request->getSchemaVersion())->toBe(SchemaVersion::V1_0);
    expect($request->getConditions())->toBe($conditions);
});

it('generates correct request context with minimal parameters', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);
    $typeDefinitions->shouldReceive('jsonSerialize')
        ->once()
        ->andReturn([
            ['type' => 'user'],
            ['type' => 'document', 'relations' => ['viewer' => 'user']]
        ]);

    $stream = Mockery::mock(StreamInterface::class);

    /** @var StreamFactoryInterface&MockInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type_definitions' => [
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']]
            ],
            'schema_version' => '1.1',
        ]))
        ->andReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});

it('generates correct request context with all parameters', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);
    $typeDefinitions->shouldReceive('jsonSerialize')
        ->once()
        ->andReturn([
            ['type' => 'user'],
            ['type' => 'document', 'relations' => ['viewer' => 'user']]
        ]);

    $conditions = Mockery::mock(ConditionsInterface::class);
    $conditions->shouldReceive('jsonSerialize')
        ->once()
        ->andReturn([
            'condition1' => ['name' => 'condition1', 'expression' => 'user.id == "123"']
        ]);

    $stream = Mockery::mock(StreamInterface::class);

    /** @var StreamFactoryInterface&MockInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type_definitions' => [
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']]
            ],
            'schema_version' => '1.0',
            'conditions' => [
                'condition1' => ['name' => 'condition1', 'expression' => 'user.id == "123"']
            ],
        ]))
        ->andReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_0,
        conditions: $conditions
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});

it('handles different schema versions', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);
    $typeDefinitions->shouldReceive('jsonSerialize')
        ->times(2)
        ->andReturn([['type' => 'user']]);

    $stream = Mockery::mock(StreamInterface::class);

    /** @var StreamFactoryInterface&MockInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    // Test each schema version
    foreach (SchemaVersion::cases() as $schemaVersion) {
        $streamFactory->shouldReceive('createStream')
            ->once()
            ->with(json_encode([
                'type_definitions' => [['type' => 'user']],
                'schema_version' => $schemaVersion->value,
            ]))
            ->andReturn($stream);

        $request = new CreateAuthorizationModelRequest(
            store: 'test-store',
            typeDefinitions: $typeDefinitions,
            schemaVersion: $schemaVersion
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
        expect($context->getBody())->toBe($stream);
    }
});

it('filters out null conditions from request body', function (): void {
    $typeDefinitions = Mockery::mock(TypeDefinitionsInterface::class);
    $typeDefinitions->shouldReceive('jsonSerialize')
        ->once()
        ->andReturn([['type' => 'user']]);

    $stream = Mockery::mock(StreamInterface::class);

    /** @var StreamFactoryInterface&MockInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type_definitions' => [['type' => 'user']],
            'schema_version' => '1.1',
        ]))
        ->andReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_1,
        conditions: null
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});
