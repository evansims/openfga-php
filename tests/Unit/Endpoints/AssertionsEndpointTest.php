<?php

declare(strict_types=1);

use OpenFGA\Models\Assertions;
use OpenFGA\RequestOptions\{ReadAssertionsOptions, WriteAssertionsOptions};
use OpenFGA\Responses\{ReadAssertionsResponse, WriteAssertionsResponse};
use OpenFGA\Exceptions\ApiEndpointException;

it('reads assertions successfully', function () {
    $storeId = 'test_store_id';
    $authModelId = 'test_auth_model_id';
    $mockResponse = [
        'authorization_model_id' => $authModelId,
        'assertions' => [
            ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
            ["tuple_key" => ["user" => "user:bob", "relation" => "writer", "object" => "document:budget"], "expectation" => false],
        ]
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->readAssertions($authModelId, $storeId);

    expect($response)->toBeInstanceOf(ReadAssertionsResponse::class)
        ->and($response->assertions)->toBeInstanceOf(Assertions::class)
        ->and($response->assertions->toArray())->toHaveCount(2)
        ->and($response->assertions->toArray()[0]['tuple_key']['user'])->toBe('user:anne')
        ->and($response->assertions->toArray()[0]['expectation'])->toBeTrue()
        ->and($response->assertions->toArray()[1]['tuple_key']['user'])->toBe('user:bob')
        ->and($response->assertions->toArray()[1]['expectation'])->toBeFalse();

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/assertions/' . $authModelId);
});

it('reads assertions with options successfully', function () {
    $storeId = 'custom_store_id';
    $authModelId = 'custom_auth_model_id';
    $mockResponse = [
        'authorization_model_id' => $authModelId,
        'assertions' => [
            ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
        ]
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $options = new ReadAssertionsOptions();
    $response = $this->client->readAssertions($authModelId, $storeId, $options);

    expect($response)->toBeInstanceOf(ReadAssertionsResponse::class)
        ->and($response->assertions->toArray())->toHaveCount(1);

    $this->assertLastRequest('GET', '/stores/' . $storeId . '/assertions/' . $authModelId);
});

it('reads assertions using default store and authorization model IDs', function () {
    $mockResponse = [
        'authorization_model_id' => 'test_auth_model_id',
        'assertions' => [
            ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
        ]
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->readAssertions();

    expect($response)->toBeInstanceOf(ReadAssertionsResponse::class)
        ->and($response->assertions->toArray())->toHaveCount(1);

    // Should use default IDs from configuration
    $this->assertLastRequest('GET', '/stores/test_store_id/assertions/test_auth_model_id');
});

it('writes assertions successfully', function () {
    $storeId = 'test_store_id';
    $authModelId = 'test_auth_model_id';
    $mockResponse = [];
    $this->mockHttpResponse(204, $mockResponse);

    $assertions = new Assertions([
        ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
        ["tuple_key" => ["user" => "user:bob", "relation" => "writer", "object" => "document:budget"], "expectation" => false],
    ]);

    $response = $this->client->writeAssertions($assertions, $authModelId, $storeId);

    expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);

    $this->assertLastRequest(
        'PUT',
        '/stores/' . $storeId . '/assertions/' . $authModelId,
        ['assertions' => $assertions->toArray()]
    );
});

it('writes assertions with options successfully', function () {
    $storeId = 'custom_store_id';
    $authModelId = 'custom_auth_model_id';
    $mockResponse = [];
    $this->mockHttpResponse(204, $mockResponse);

    $assertions = new Assertions([
        ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
    ]);

    $options = new WriteAssertionsOptions();
    $response = $this->client->writeAssertions($assertions, $authModelId, $storeId, $options);

    expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);

    $this->assertLastRequest(
        'PUT',
        '/stores/' . $storeId . '/assertions/' . $authModelId,
        ['assertions' => $assertions->toArray()]
    );
});

it('writes assertions using default store and authorization model IDs', function () {
    $mockResponse = [];
    $this->mockHttpResponse(204, $mockResponse);

    $assertions = new Assertions([
        ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
    ]);

    $response = $this->client->writeAssertions($assertions);

    expect($response)->toBeInstanceOf(WriteAssertionsResponse::class);

    // Should use default IDs from configuration
    $this->assertLastRequest(
        'PUT',
        '/stores/test_store_id/assertions/test_auth_model_id',
        ['assertions' => $assertions->toArray()]
    );
});

it('throws API errors when reading assertions', function () {
    $storeId = 'invalid_store';
    $authModelId = 'invalid_model';
    $mockResponse = ['code' => 'not_found', 'message' => 'Store not found'];
    $this->mockHttpResponse(404, $mockResponse);

    $this->client->readAssertions($authModelId, $storeId);
})->throws(ApiEndpointException::class);

it('throws API errors when writing assertions', function () {
    $storeId = 'invalid_store';
    $authModelId = 'invalid_model';
    $mockResponse = ['code' => 'not_found', 'message' => 'Store not found'];
    $this->mockHttpResponse(404, $mockResponse);

    $assertions = new Assertions([
        ["tuple_key" => ["user" => "user:anne", "relation" => "reader", "object" => "document:budget"], "expectation" => true],
    ]);

    $this->client->writeAssertions($assertions, $authModelId, $storeId);
})->throws(ApiEndpointException::class);
