<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\CreateAuthorizationModelRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

it('can be instantiated with required parameters', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
    );

    expect($request)->toBeInstanceOf(CreateAuthorizationModelRequest::class);
    expect($request->getStore())->toBe('test-store');
    expect($request->getTypeDefinitions())->toBe($typeDefinitions);
    expect($request->getSchemaVersion())->toBe(SchemaVersion::V1_1); // Default value
    expect($request->getConditions())->toBeNull();
});

it('can be instantiated with all parameters', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
    $conditions = test()->createMock(ConditionsInterface::class);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_0,
        conditions: $conditions,
    );

    expect($request->getStore())->toBe('test-store');
    expect($request->getTypeDefinitions())->toBe($typeDefinitions);
    expect($request->getSchemaVersion())->toBe(SchemaVersion::V1_0);
    expect($request->getConditions())->toBe($conditions);
});

it('generates correct request context with minimal parameters', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
    $typeDefinitions->method('jsonSerialize')
        ->willReturn([
            ['type' => 'user'],
            ['type' => 'document', 'relations' => ['viewer' => 'user']],
        ]);

    $stream = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->once())
        ->method('createStream')
        ->with(json_encode([
            'type_definitions' => [
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']],
            ],
            'schema_version' => '1.1',
        ]))
        ->willReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});

it('generates correct request context with all parameters', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
    $typeDefinitions->method('jsonSerialize')
        ->willReturn([
            ['type' => 'user'],
            ['type' => 'document', 'relations' => ['viewer' => 'user']],
        ]);

    $conditions = test()->createMock(ConditionsInterface::class);
    $conditions->method('jsonSerialize')
        ->willReturn([
            'condition1' => ['name' => 'condition1', 'expression' => 'user.id == "123"'],
        ]);

    $stream = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->once())
        ->method('createStream')
        ->with(json_encode([
            'type_definitions' => [
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']],
            ],
            'schema_version' => '1.0',
            'conditions' => [
                'condition1' => ['name' => 'condition1', 'expression' => 'user.id == "123"'],
            ],
        ]))
        ->willReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_0,
        conditions: $conditions,
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});

it('handles different schema versions', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
    $typeDefinitions->method('jsonSerialize')
        ->willReturn([['type' => 'user']]);

    $stream = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);

    // Set up expectations for each schema version call
    $streamFactory->expects(test()->exactly(2))
        ->method('createStream')
        ->willReturnCallback(function (string $json) use ($stream): StreamInterface {
            $data = json_decode($json, true);
            expect($data['type_definitions'])->toBe([['type' => 'user']]);
            expect($data['schema_version'])->toBeIn(['1.0', '1.1']);

            return $stream;
        });

    // Test each schema version
    foreach (SchemaVersion::cases() as $schemaVersion) {
        $request = new CreateAuthorizationModelRequest(
            store: 'test-store',
            typeDefinitions: $typeDefinitions,
            schemaVersion: $schemaVersion,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
        expect($context->getBody())->toBe($stream);
    }
});

it('filters out null conditions from request body', function (): void {
    $typeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
    $typeDefinitions->method('jsonSerialize')
        ->willReturn([['type' => 'user']]);

    $stream = test()->createMock(StreamInterface::class);

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->once())
        ->method('createStream')
        ->with(json_encode([
            'type_definitions' => [['type' => 'user']],
            'schema_version' => '1.1',
        ]))
        ->willReturn($stream);

    $request = new CreateAuthorizationModelRequest(
        store: 'test-store',
        typeDefinitions: $typeDefinitions,
        schemaVersion: SchemaVersion::V1_1,
        conditions: null,
    );

    $context = $request->getRequest($streamFactory);

    expect($context->getMethod())->toBe(RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/authorization-models');
    expect($context->getBody())->toBe($stream);
});

it('throws when store ID is empty', function (): void {
    expect(fn () => new CreateAuthorizationModelRequest(store: '', typeDefinitions: test()->createMock(TypeDefinitionsInterface::class)))
        ->toThrow(InvalidArgumentException::class);
});
