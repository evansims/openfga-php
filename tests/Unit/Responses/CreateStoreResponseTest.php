<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use DateTimeImmutable;
use DateTimeZone;
use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Responses\{CreateStoreResponse, CreateStoreResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

use function in_array;
use function strlen;

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

    test('fromResponse handles error responses with non-201 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 401 unauthorized', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Invalid credentials']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 403 forbidden', function (): void {
        $httpResponse = new SimpleResponse(403, json_encode(['code' => 'forbidden', 'message' => 'Access denied']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 409 conflict', function (): void {
        $httpResponse = new SimpleResponse(409, json_encode(['code' => 'store_already_exists', 'message' => 'Store already exists']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 422 unprocessable entity', function (): void {
        $httpResponse = new SimpleResponse(422, json_encode(['code' => 'validation_error', 'message' => 'Invalid store name']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 200 status (wrong status code)', function (): void {
        $httpResponse = new SimpleResponse(200, json_encode(['id' => 'store-123', 'name' => 'Test Store']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        CreateStoreResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('schema returns expected structure', function (): void {
        $schema = CreateStoreResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(CreateStoreResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        expect($properties)->toHaveKeys(['id', 'name', 'created_at', 'updated_at']);

        foreach ($properties as $key => $property) {
            expect($property->required)->toBeTrue();
            if (in_array($key, ['created_at', 'updated_at'], true)) {
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

    test('handles special characters in store name', function (): void {
        $specialName = 'Test Store™ © 2024 - "Special" & Symbols!';
        $response = new CreateStoreResponse(
            'store-123',
            $specialName,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response->getName())->toBe($specialName);
    });

    test('handles very long store names', function (): void {
        $longName = str_repeat('Long Store Name ', 100);
        $response = new CreateStoreResponse(
            'store-123',
            $longName,
            new DateTimeImmutable(),
            new DateTimeImmutable(),
        );
        expect($response->getName())->toBe($longName);
        expect(strlen($response->getName()))->toBe(1600);
    });

    test('handles timestamps in different timezones', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('America/New_York'));
        $updatedAt = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('Europe/London'));

        $response = new CreateStoreResponse(
            'store-id',
            'Test Store',
            $createdAt,
            $updatedAt,
        );

        expect($response->getCreatedAt()->getTimezone()->getName())->toBe('America/New_York');
        expect($response->getUpdatedAt()->getTimezone()->getName())->toBe('Europe/London');
    });
});
