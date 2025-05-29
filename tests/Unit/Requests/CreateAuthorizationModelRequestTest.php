<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{ConditionsInterface, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\CreateAuthorizationModelRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('CreateAuthorizationModelRequest', function (): void {
    test('can be instantiated with required parameters', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);

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

    test('can be instantiated with all parameters', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);
        $conditions = $this->createMock(ConditionsInterface::class);

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

    test('generates correct request context with minimal parameters', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);
        $typeDefinitions->method('jsonSerialize')
            ->willReturn([
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']],
            ]);

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with(json_encode([
                'schema_version' => '1.1',
                'type_definitions' => [
                    ['type' => 'user'],
                    ['type' => 'document', 'relations' => ['viewer' => 'user']],
                ],
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

    test('generates correct request context with all parameters', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);
        $typeDefinitions->method('jsonSerialize')
            ->willReturn([
                ['type' => 'user'],
                ['type' => 'document', 'relations' => ['viewer' => 'user']],
            ]);

        $conditions = $this->createMock(ConditionsInterface::class);
        $conditions->method('count')
            ->willReturn(1);
        $conditions->method('jsonSerialize')
            ->willReturn([
                'condition1' => ['name' => 'condition1', 'expression' => 'user.id == "123"'],
            ]);

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with(json_encode([
                'schema_version' => '1.0',
                'type_definitions' => [
                    ['type' => 'user'],
                    ['type' => 'document', 'relations' => ['viewer' => 'user']],
                ],
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

    test('handles different schema versions', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);
        $typeDefinitions->method('jsonSerialize')
            ->willReturn([['type' => 'user']]);

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);

        // Set up expectations for each schema version call
        $streamFactory->expects($this->exactly(2))
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

    test('filters out null conditions from request body', function (): void {
        $typeDefinitions = $this->createMock(TypeDefinitionsInterface::class);
        $typeDefinitions->method('jsonSerialize')
            ->willReturn([['type' => 'user']]);

        $stream = $this->createMock(StreamInterface::class);

        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->expects($this->once())
            ->method('createStream')
            ->with(json_encode([
                'schema_version' => '1.1',
                'type_definitions' => [['type' => 'user']],
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

    test('throws when store ID is empty', function (): void {
        new CreateAuthorizationModelRequest(store: '', typeDefinitions: $this->createMock(TypeDefinitionsInterface::class));
    })->throws(InvalidArgumentException::class);
});
