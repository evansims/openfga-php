<?php

declare(strict_types=1);

use OpenFGA\Models\Store;
use OpenFGA\Responses\{GetStoreResponse, GetStoreResponseInterface};

test('GetStoreResponse implements GetStoreResponseInterface', function (): void {
    $response = new GetStoreResponse(
        'store-id',
        'store-name',
        new DateTimeImmutable(),
        new DateTimeImmutable()
    );
    expect($response)->toBeInstanceOf(GetStoreResponseInterface::class);
});

test('GetStoreResponse constructs and returns properties without deletedAt', function (): void {
    $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
    
    $response = new GetStoreResponse(
        id: 'store-123',
        name: 'Test Store',
        createdAt: $createdAt,
        updatedAt: $updatedAt
    );
    
    expect($response->getId())->toBe('store-123');
    expect($response->getName())->toBe('Test Store');
    expect($response->getCreatedAt())->toBe($createdAt);
    expect($response->getUpdatedAt())->toBe($updatedAt);
    expect($response->getDeletedAt())->toBeNull();
});

test('GetStoreResponse constructs and returns properties with deletedAt', function (): void {
    $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
    $deletedAt = new DateTimeImmutable('2024-01-03 09:00:00');
    
    $response = new GetStoreResponse(
        id: 'store-456',
        name: 'Deleted Store',
        createdAt: $createdAt,
        updatedAt: $updatedAt,
        deletedAt: $deletedAt
    );
    
    expect($response->getId())->toBe('store-456');
    expect($response->getName())->toBe('Deleted Store');
    expect($response->getCreatedAt())->toBe($createdAt);
    expect($response->getUpdatedAt())->toBe($updatedAt);
    expect($response->getDeletedAt())->toBe($deletedAt);
});

test('GetStoreResponse getStore returns Store object', function (): void {
    $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
    
    $response = new GetStoreResponse(
        id: 'store-789',
        name: 'Store Object Test',
        createdAt: $createdAt,
        updatedAt: $updatedAt
    );
    
    $store = $response->getStore();
    
    expect($store)->toBeInstanceOf(Store::class);
    expect($store->getId())->toBe('store-789');
    expect($store->getName())->toBe('Store Object Test');
    expect($store->getCreatedAt())->toBe($createdAt);
    expect($store->getUpdatedAt())->toBe($updatedAt);
    expect($store->getDeletedAt())->toBeNull();
});

test('GetStoreResponse getStore returns Store object with deletedAt', function (): void {
    $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
    $deletedAt = new DateTimeImmutable('2024-01-03 09:00:00');
    
    $response = new GetStoreResponse(
        id: 'deleted-store',
        name: 'Deleted Store Test',
        createdAt: $createdAt,
        updatedAt: $updatedAt,
        deletedAt: $deletedAt
    );
    
    $store = $response->getStore();
    
    expect($store)->toBeInstanceOf(Store::class);
    expect($store->getDeletedAt())->toBe($deletedAt);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('GetStoreResponse schema returns expected structure', function (): void {
    $schema = GetStoreResponse::schema();
    
    expect($schema)->toBeInstanceOf(\OpenFGA\Schema\SchemaInterface::class);
    expect($schema->getClassName())->toBe(GetStoreResponse::class);
    
    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(5);
    
    expect($properties)->toHaveKeys(['id', 'name', 'created_at', 'updated_at', 'deleted_at']);
    
    // Check required properties
    expect($properties['id']->required)->toBeTrue();
    expect($properties['name']->required)->toBeTrue();
    expect($properties['created_at']->required)->toBeTrue();
    expect($properties['updated_at']->required)->toBeTrue();
    expect($properties['deleted_at']->required)->toBeFalse();
    
    // Check datetime formats
    expect($properties['created_at']->format)->toBe('datetime');
    expect($properties['updated_at']->format)->toBe('datetime');
    expect($properties['deleted_at']->format)->toBe('datetime');
});

test('GetStoreResponse schema is cached', function (): void {
    $schema1 = GetStoreResponse::schema();
    $schema2 = GetStoreResponse::schema();
    
    expect($schema1)->toBe($schema2);
});

test('GetStoreResponse handles empty store name', function (): void {
    $response = new GetStoreResponse(
        'id-123',
        '',
        new DateTimeImmutable(),
        new DateTimeImmutable()
    );
    expect($response->getName())->toBe('');
});

test('GetStoreResponse handles UUID format store ID', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $response = new GetStoreResponse(
        $uuid,
        'UUID Store',
        new DateTimeImmutable(),
        new DateTimeImmutable()
    );
    expect($response->getId())->toBe($uuid);
});

test('GetStoreResponse preserves exact timestamp precision', function (): void {
    $createdAt = new DateTimeImmutable('2024-01-01 10:00:00.123456');
    $updatedAt = new DateTimeImmutable('2024-01-01 10:00:00.789012');
    $deletedAt = new DateTimeImmutable('2024-01-01 10:00:00.555555');
    
    $response = new GetStoreResponse(
        'store-id',
        'Test Store',
        $createdAt,
        $updatedAt,
        $deletedAt
    );
    
    expect($response->getCreatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.123456');
    expect($response->getUpdatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.789012');
    expect($response->getDeletedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.555555');
});