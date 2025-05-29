<?php

declare(strict_types=1);

use OpenFGA\Responses\{CreateStoreResponse, CreateStoreResponseInterface};

describe('CreateStoreResponse', function (): void {
    test('implements CreateStoreResponseInterface', function (): void {
        $response = new CreateStoreResponse(
            'store-id',
            'store-name',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response)->toBeInstanceOf(CreateStoreResponseInterface::class);
    });

    test('constructs and returns properties', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');

        $response = new CreateStoreResponse(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        expect($response->getId())->toBe('store-123');
        expect($response->getName())->toBe('Test Store');
        expect($response->getCreatedAt())->toBe($createdAt);
        expect($response->getUpdatedAt())->toBe($updatedAt);
    });

    // Note: fromResponse method testing would require integration tests due to SchemaValidator being final
    // These tests focus on the model's direct functionality

    test('schema returns expected structure', function (): void {
        $schema = CreateStoreResponse::schema();

        expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
        expect($schema->getClassName())->toBe(CreateStoreResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        expect($properties)->toHaveKeys(['id', 'name', 'created_at', 'updated_at']);

        foreach ($properties as $key => $property) {
            expect($property->required)->toBeTrue();
            if (\in_array($key, ['created_at', 'updated_at'], true)) {
                expect($property->format)->toBe('datetime');
            }
        }
    });

    test('schema is cached', function (): void {
        $schema1 = CreateStoreResponse::schema();
        $schema2 = CreateStoreResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty store name', function (): void {
        $response = new CreateStoreResponse(
            'id-123',
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response->getName())->toBe('');
    });

    test('handles UUID format store ID', function (): void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = new CreateStoreResponse(
            $uuid,
            'UUID Store',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response->getId())->toBe($uuid);
    });

    test('preserves exact timestamp precision', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00.123456');
        $updatedAt = new DateTimeImmutable('2024-01-01 10:00:00.789012');

        $response = new CreateStoreResponse(
            'store-id',
            'Test Store',
            $createdAt,
            $updatedAt,
        );

        expect($response->getCreatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.123456');
        expect($response->getUpdatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.789012');
    });
});
