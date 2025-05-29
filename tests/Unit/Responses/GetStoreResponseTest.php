<?php

declare(strict_types=1);

use OpenFGA\Models\Store;
use OpenFGA\Responses\{GetStoreResponse, GetStoreResponseInterface};
use OpenFGA\Schema\SchemaValidator;
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('GetStoreResponse', function (): void {
    test('implements GetStoreResponseInterface', function (): void {
        $response = new GetStoreResponse(
            'store-id',
            'store-name',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response)->toBeInstanceOf(GetStoreResponseInterface::class);
    });

    test('constructs and returns properties without deletedAt', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');

        $response = new GetStoreResponse(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        expect($response->getId())->toBe('store-123');
        expect($response->getName())->toBe('Test Store');
        expect($response->getCreatedAt())->toBe($createdAt);
        expect($response->getUpdatedAt())->toBe($updatedAt);
        expect($response->getDeletedAt())->toBeNull();
    });

    test('constructs and returns properties with deletedAt', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
        $deletedAt = new DateTimeImmutable('2024-01-03 09:00:00');

        $response = new GetStoreResponse(
            id: 'store-456',
            name: 'Deleted Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt,
        );

        expect($response->getId())->toBe('store-456');
        expect($response->getName())->toBe('Deleted Store');
        expect($response->getCreatedAt())->toBe($createdAt);
        expect($response->getUpdatedAt())->toBe($updatedAt);
        expect($response->getDeletedAt())->toBe($deletedAt);
    });

    test('getStore returns Store object', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');

        $response = new GetStoreResponse(
            id: 'store-789',
            name: 'Store Object Test',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $store = $response->getStore();

        expect($store)->toBeInstanceOf(Store::class);
        expect($store->getId())->toBe('store-789');
        expect($store->getName())->toBe('Store Object Test');
        expect($store->getCreatedAt())->toBe($createdAt);
        expect($store->getUpdatedAt())->toBe($updatedAt);
        expect($store->getDeletedAt())->toBeNull();
    });

    test('getStore returns Store object with deletedAt', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-02 15:30:00');
        $deletedAt = new DateTimeImmutable('2024-01-03 09:00:00');

        $response = new GetStoreResponse(
            id: 'deleted-store',
            name: 'Deleted Store Test',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt,
        );

        $store = $response->getStore();

        expect($store)->toBeInstanceOf(Store::class);
        expect($store->getDeletedAt())->toBe($deletedAt);
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        GetStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 401 unauthorized', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Invalid credentials']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        GetStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 403 forbidden', function (): void {
        $httpResponse = new SimpleResponse(403, json_encode(['code' => 'forbidden', 'message' => 'Access denied']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        GetStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 404 not found', function (): void {
        $httpResponse = new SimpleResponse(404, json_encode(['code' => 'store_not_found', 'message' => 'Store not found']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        GetStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = Mockery::mock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(OpenFGA\Exceptions\NetworkException::class);
        GetStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('schema returns expected structure', function (): void {
        $schema = GetStoreResponse::schema();

        expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
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

    test('schema is cached', function (): void {
        $schema1 = GetStoreResponse::schema();
        $schema2 = GetStoreResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty store name', function (): void {
        $response = new GetStoreResponse(
            'id-123',
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response->getName())->toBe('');
    });

    test('handles UUID format store ID', function (): void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = new GetStoreResponse(
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
        $deletedAt = new DateTimeImmutable('2024-01-01 10:00:00.555555');

        $response = new GetStoreResponse(
            'store-id',
            'Test Store',
            $createdAt,
            $updatedAt,
            $deletedAt,
        );

        expect($response->getCreatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.123456');
        expect($response->getUpdatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.789012');
        expect($response->getDeletedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.555555');
    });

    test('getStore returns new Store instance each time', function (): void {
        $response = new GetStoreResponse(
            'store-123',
            'Test Store',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );

        $store1 = $response->getStore();
        $store2 = $response->getStore();

        expect($store1)->toBeInstanceOf(Store::class);
        expect($store2)->toBeInstanceOf(Store::class);
        expect($store1)->not->toBe($store2); // Different instances
        expect($store1->getId())->toBe($store2->getId()); // But same data
        expect($store1->getName())->toBe($store2->getName());
    });

    test('handles store with very long name', function (): void {
        $longName = str_repeat('Very Long Store Name ', 100);
        $response = new GetStoreResponse(
            'store-123',
            $longName,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );

        expect($response->getName())->toBe($longName);
        expect($response->getStore()->getName())->toBe($longName);
    });

    test('handles timestamps in different timezones', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('America/New_York'));
        $updatedAt = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('Europe/London'));
        $deletedAt = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('Asia/Tokyo'));

        $response = new GetStoreResponse(
            'store-id',
            'Test Store',
            $createdAt,
            $updatedAt,
            $deletedAt,
        );

        expect($response->getCreatedAt()->getTimezone()->getName())->toBe('America/New_York');
        expect($response->getUpdatedAt()->getTimezone()->getName())->toBe('Europe/London');
        expect($response->getDeletedAt()->getTimezone()->getName())->toBe('Asia/Tokyo');

        // Store should preserve timezones
        $store = $response->getStore();
        expect($store->getCreatedAt()->getTimezone()->getName())->toBe('America/New_York');
        expect($store->getUpdatedAt()->getTimezone()->getName())->toBe('Europe/London');
        expect($store->getDeletedAt()->getTimezone()->getName())->toBe('Asia/Tokyo');
    });
});
