<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Contract;

use OpenFGA\Models\{Assertion, AssertionTupleKey, AuthorizationModel, Store, Tuple, TupleKey, TypeDefinition, User};
use OpenFGA\Models\Collections\{TypeDefinitionRelations, TypeDefinitions};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schema\SchemaValidator;
use RuntimeException;

use function array_key_exists;
use function count;
use function dirname;
use function strlen;

describe('OpenAPI Contract Validation', function (): void {
    beforeEach(function (): void {
        $specPath = dirname(__DIR__) . '/Support/openfga.openapi.swagger.json';
        $specContent = file_get_contents($specPath);

        if (false === $specContent) {
            throw new RuntimeException("Could not read OpenAPI spec file at {$specPath}");
        }
        $this->openApiSpec = json_decode($specContent, true);

        if (null === $this->openApiSpec) {
            throw new RuntimeException('Could not parse OpenAPI spec JSON');
        }

        $this->validator = new SchemaValidator;
    });

    test('Store model OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['Store'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $openApiRequired = $openApiSchema['required'] ?? [];
        expect($openApiRequired)->toContain('id');
        expect($openApiRequired)->toContain('name');
        expect($openApiRequired)->toContain('created_at');
        expect($openApiRequired)->toContain('updated_at');

        $testData = [
            'id' => 'test-store-123',
            'name' => 'Test Store',
            'created_at' => '2023-01-01T10:00:00Z',
            'updated_at' => '2023-01-02T11:00:00Z',
        ];

        $this->validator->registerSchema(Store::schema());
        $store = $this->validator->validateAndTransform($testData, Store::class);
        $serialized = $store->jsonSerialize();

        foreach ($openApiRequired as $field) {
            expect($serialized)->toHaveKey($field);
        }

        expect(array_key_exists('deleted_at', $serialized))->toBe(false);

        $testDataWithDeleted = array_merge($testData, ['deleted_at' => '2023-01-03T12:00:00Z']);
        $storeDeleted = $this->validator->validateAndTransform($testDataWithDeleted, Store::class);
        $serializedDeleted = $storeDeleted->jsonSerialize();
        expect($serializedDeleted)->toHaveKey('deleted_at');
    });

    test('TupleKey model OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['TupleKey']
                         ?? $this->openApiSpec['definitions']['CheckRequestTupleKey'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $openApiRequired = $openApiSchema['required'] ?? [];
        expect($openApiRequired)->toContain('user');
        expect($openApiRequired)->toContain('relation');
        expect($openApiRequired)->toContain('object');

        $testData = [
            'user' => 'user:123',
            'relation' => 'viewer',
            'object' => 'document:456',
        ];

        $this->validator->registerSchema(TupleKey::schema());
        $tupleKey = $this->validator->validateAndTransform($testData, TupleKey::class);
        $serialized = $tupleKey->jsonSerialize();

        foreach ($openApiRequired as $field) {
            expect($serialized)->toHaveKey($field);
        }

        $properties = $openApiSchema['properties'] ?? [];

        if (isset($properties['user']['maxLength'])) {
            expect(strlen($serialized['user']))->toBeLessThanOrEqual($properties['user']['maxLength']);
        }

        if (isset($properties['relation']['maxLength'])) {
            expect(strlen($serialized['relation']))->toBeLessThanOrEqual($properties['relation']['maxLength']);
        }

        if (isset($properties['object']['maxLength'])) {
            expect(strlen($serialized['object']))->toBeLessThanOrEqual($properties['object']['maxLength']);
        }
    });

    test('Tuple model OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['Tuple'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $properties = $openApiSchema['properties'] ?? [];
        expect($properties)->toHaveKey('key');
        expect($properties)->toHaveKey('timestamp');

        $testData = [
            'key' => [
                'user' => 'user:123',
                'relation' => 'viewer',
                'object' => 'document:456',
            ],
            'timestamp' => '2023-01-01T10:00:00Z',
        ];

        $this->validator->registerSchema(Tuple::schema());
        $this->validator->registerSchema(TupleKey::schema());
        $tuple = $this->validator->validateAndTransform($testData, Tuple::class);
        $serialized = $tuple->jsonSerialize();

        expect($serialized)->toHaveKey('key');
        expect($serialized)->toHaveKey('timestamp');
        expect($serialized['key'])->toBeArray();
        expect($serialized['key'])->toHaveKeys(['user', 'relation', 'object']);
    });

    test('AuthorizationModel OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['AuthorizationModel'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $openApiRequired = $openApiSchema['required'] ?? [];
        expect($openApiRequired)->toContain('id');
        expect($openApiRequired)->toContain('schema_version');

        $typeDefinitions = new TypeDefinitions([]);
        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );
        $serialized = $model->jsonSerialize();

        foreach ($openApiRequired as $field) {
            expect($serialized)->toHaveKey($field);
        }

        $schemaVersionEnum = $openApiSchema['properties']['schema_version']['enum'] ?? null;

        if (null !== $schemaVersionEnum) {
            expect($schemaVersionEnum)->toContain($serialized['schema_version']);
        }
    });

    test('TypeDefinition model OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['TypeDefinition'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $openApiRequired = $openApiSchema['required'] ?? [];
        expect($openApiRequired)->toContain('type');

        $relations = new TypeDefinitionRelations([]);
        $typeDef = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );
        $serialized = $typeDef->jsonSerialize();

        expect($serialized)->toHaveKey('type');

        if (0 < $relations->count()) {
            expect($serialized)->toHaveKey('relations');
        }
    });

    test('User model OpenAPI schema union type', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['User'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $properties = $openApiSchema['properties'] ?? [];
        expect($properties)->toHaveKey('object');
        expect($properties)->toHaveKey('userset');
        expect($properties)->toHaveKey('wildcard');

        $testData = [
            'object' => [
                'type' => 'user',
                'id' => '123',
            ],
        ];

        $this->validator->registerSchema(User::schema());
        $user = $this->validator->validateAndTransform($testData, User::class);
        $serialized = $user->jsonSerialize();

        // Verify only one variant is present
        $presentFields = array_filter(['object', 'userset', 'wildcard'], fn ($field) => isset($serialized[$field]));
        expect(count($presentFields))->toBe(1);
    });

    test('Assertion model OpenAPI schema', function (): void {
        $openApiSchema = $this->openApiSpec['definitions']['Assertion'] ?? null;
        expect($openApiSchema)->not->toBeNull();

        $properties = $openApiSchema['properties'] ?? [];
        expect($properties)->toHaveKey('tuple_key');
        expect($properties)->toHaveKey('expectation');

        $tupleKey = new AssertionTupleKey(
            user: 'user:123',
            relation: 'viewer',
            object: 'document:456',
        );
        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
        );
        $serialized = $assertion->jsonSerialize();

        expect($serialized)->toHaveKey('tuple_key');
        expect($serialized)->toHaveKey('expectation');
        expect($serialized['expectation'])->toBeBool();
    });

    test('response models structure', function (): void {
        $listStoresSchema = $this->openApiSpec['definitions']['ListStoresResponse'] ?? null;
        expect($listStoresSchema)->not->toBeNull();
        expect($listStoresSchema['properties'])->toHaveKey('stores');
        expect($listStoresSchema['properties']['stores']['type'])->toBe('array');
        expect($listStoresSchema['properties']['stores']['items']['$ref'])->toContain('Store');

        $checkResponseSchema = $this->openApiSpec['definitions']['CheckResponse'] ?? null;
        expect($checkResponseSchema)->not->toBeNull();
        expect($checkResponseSchema['properties'])->toHaveKey('allowed');
        expect($checkResponseSchema['properties']['allowed']['type'])->toBe('boolean');

        $createStoreSchema = $this->openApiSpec['definitions']['CreateStoreResponse'] ?? null;
        expect($createStoreSchema)->not->toBeNull();
        $requiredFields = $createStoreSchema['required'] ?? [];
        expect($requiredFields)->toContain('id');
        expect($requiredFields)->toContain('name');
        expect($requiredFields)->toContain('created_at');
        expect($requiredFields)->toContain('updated_at');
    });

    test('request models OpenAPI schema', function (): void {
        $createStoreRequest = $this->openApiSpec['definitions']['CreateStoreRequest'] ?? null;
        expect($createStoreRequest)->not->toBeNull();
        expect($createStoreRequest['required'] ?? [])->toContain('name');

        // Check request is defined inline in the path, not as a separate definition
        $checkPath = $this->openApiSpec['paths']['/stores/{store_id}/check']['post'] ?? null;
        expect($checkPath)->not->toBeNull();
        $checkBodyParam = null;

        foreach ($checkPath['parameters'] ?? [] as $param) {
            if ('body' === $param['name'] && 'body' === $param['in']) {
                $checkBodyParam = $param;

                break;
            }
        }
        expect($checkBodyParam)->not->toBeNull();
        expect($checkBodyParam['schema']['properties'])->toHaveKey('tuple_key');
        expect($checkBodyParam['schema']['required'] ?? [])->toContain('tuple_key');
    });

    test('model serialization roundtrip', function (): void {
        $storeData = [
            'id' => 'store-123',
            'name' => 'Test Store',
            'created_at' => '2023-01-01T10:00:00Z',
            'updated_at' => '2023-01-02T11:00:00Z',
        ];

        $this->validator->registerSchema(Store::schema());
        $store = $this->validator->validateAndTransform($storeData, Store::class);
        $serialized = $store->jsonSerialize();

        expect($serialized['id'])->toBe($storeData['id']);
        expect($serialized['name'])->toBe($storeData['name']);

        $store2 = $this->validator->validateAndTransform($serialized, Store::class);
        expect($store2->jsonSerialize())->toBe($serialized);
    });

    test('field constraints from OpenAPI spec', function (): void {
        $tupleKeySchema = $this->openApiSpec['definitions']['TupleKey']
                          ?? $this->openApiSpec['definitions']['CheckRequestTupleKey'] ?? null;

        if (null !== $tupleKeySchema) {
            $userMaxLength = $tupleKeySchema['properties']['user']['maxLength'] ?? null;
            $relationMaxLength = $tupleKeySchema['properties']['relation']['maxLength'] ?? null;
            $objectMaxLength = $tupleKeySchema['properties']['object']['maxLength'] ?? null;

            expect($userMaxLength)->toBe(512);
            expect($relationMaxLength)->toBe(50);
            expect($objectMaxLength)->toBe(256);
        }
    });

    test('enum types OpenAPI spec', function (): void {
        $authModelSchema = $this->openApiSpec['definitions']['AuthorizationModel'] ?? null;
        expect($authModelSchema)->not->toBeNull();
        expect($authModelSchema['properties']['schema_version']['type'])->toBe('string');

        $tupleOperationSchema = $this->openApiSpec['definitions']['TupleOperation'] ?? null;
        expect($tupleOperationSchema)->not->toBeNull();
        expect($tupleOperationSchema['enum'])->toBeArray();
        expect($tupleOperationSchema['enum'])->toContain('TUPLE_OPERATION_WRITE');
        expect($tupleOperationSchema['enum'])->toContain('TUPLE_OPERATION_DELETE');

        $consistencySchema = $this->openApiSpec['definitions']['ConsistencyPreference'] ?? null;
        expect($consistencySchema)->not->toBeNull();
        expect($consistencySchema['enum'])->toBeArray();
        expect($consistencySchema['enum'])->toContain('UNSPECIFIED');
    });

    test('collection responses OpenAPI array schemas', function (): void {
        $listStoresSchema = $this->openApiSpec['definitions']['ListStoresResponse'] ?? null;
        expect($listStoresSchema)->not->toBeNull();

        $storesProperty = $listStoresSchema['properties']['stores'] ?? null;
        expect($storesProperty['type'])->toBe('array');
        expect($storesProperty['items'])->toHaveKey('$ref');

        $listModelsSchema = $this->openApiSpec['definitions']['ReadAuthorizationModelsResponse'] ?? null;
        expect($listModelsSchema)->not->toBeNull();

        $modelsProperty = $listModelsSchema['properties']['authorization_models'] ?? null;
        expect($modelsProperty['type'])->toBe('array');
        expect($modelsProperty['items'])->toHaveKey('$ref');
    });

    test('nullable fields OpenAPI spec', function (): void {
        $storeSchema = $this->openApiSpec['definitions']['Store'] ?? null;

        if (null !== $storeSchema) {
            $required = $storeSchema['required'] ?? [];
            expect($required)->not->toContain('deleted_at');
        }
    });

    test('datetime format fields OpenAPI spec', function (): void {
        $storeSchema = $this->openApiSpec['definitions']['Store'] ?? null;

        if (null !== $storeSchema) {
            $dateFields = ['created_at', 'updated_at', 'deleted_at'];

            foreach ($dateFields as $field) {
                if (isset($storeSchema['properties'][$field])) {
                    $property = $storeSchema['properties'][$field];
                    expect($property['type'])->toBe('string');
                    expect($property['format'])->toBe('date-time');
                }
            }
        }
    });
});
