<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Context\Context;
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\StoreInterface;
use RuntimeException;

/**
 * @covers \OpenFGA\Context\Context
 */

// Reset the context state before each test
beforeEach(function () {
    while (Context::hasContext()) {
        Context::with(fn() => null);
    }
});

test('hasContext returns false when no context is active', function () {
    expect(Context::hasContext())->toBeFalse();
});

test('hasContext returns true when context is active', function () {
    Context::with(function () {
        expect(Context::hasContext())->toBeTrue();
    });
});

test('current throws exception when no context is active', function () {
    expect(fn() => Context::current())
        ->toThrow(RuntimeException::class, 'No context is currently active. Wrap your code in Context::with() or use the context helper method.');
});

test('depth returns zero when no context is active', function () {
    expect(Context::depth())->toBe(0);
});

test('depth increases with nested contexts', function () {
    Context::with(function () {
        expect(Context::depth())->toBe(1);
        
        Context::with(function () {
            expect(Context::depth())->toBe(2);
            
            Context::with(function () {
                expect(Context::depth())->toBe(3);
            });
            
            expect(Context::depth())->toBe(2);
        });
        
        expect(Context::depth())->toBe(1);
    });
    
    expect(Context::depth())->toBe(0);
});

test('getClient returns null when no context is active', function () {
    expect(Context::getClient())->toBeNull();
});

test('getClient returns client from context', function () {
    $client = $this->createMock(ClientInterface::class);
    
    Context::with(function () use ($client) {
        expect(Context::getClient())->toBe($client);
    }, client: $client);
});

test('getStore returns null when no context is active', function () {
    expect(Context::getStore())->toBeNull();
});

test('getStore returns store from context', function () {
    $store = $this->createMock(StoreInterface::class);
    
    Context::with(function () use ($store) {
        expect(Context::getStore())->toBe($store);
    }, store: $store);
});

test('getStore returns string store from context', function () {
    $store = 'store-id';
    
    Context::with(function () use ($store) {
        expect(Context::getStore())->toBe($store);
    }, store: $store);
});

test('getModel returns null when no context is active', function () {
    expect(Context::getModel())->toBeNull();
});

test('getModel returns model from context', function () {
    $model = $this->createMock(AuthorizationModelInterface::class);
    
    Context::with(function () use ($model) {
        expect(Context::getModel())->toBe($model);
    }, model: $model);
});

test('getModel returns string model from context', function () {
    $model = 'model-id';
    
    Context::with(function () use ($model) {
        expect(Context::getModel())->toBe($model);
    }, model: $model);
});

test('getPrevious returns null when no context is active', function () {
    expect(Context::getPrevious())->toBeNull();
});

test('getPrevious returns null for top level context', function () {
    Context::with(function () {
        expect(Context::getPrevious())->toBeNull();
    });
});

test('nested context inherits values from parent', function () {
    $client = $this->createMock(ClientInterface::class);
    $store = $this->createMock(StoreInterface::class);
    $model = $this->createMock(AuthorizationModelInterface::class);
    
    Context::with(function () use ($client, $store, $model) {
        expect(Context::getClient())->toBe($client);
        expect(Context::getStore())->toBe($store);
        expect(Context::getModel())->toBe($model);
        
        Context::with(function () use ($client, $store, $model) {
            expect(Context::getClient())->toBe($client);
            expect(Context::getStore())->toBe($store);
            expect(Context::getModel())->toBe($model);
        });
    }, client: $client, store: $store, model: $model);
});

test('nested context can override parent values', function () {
    $client1 = $this->createMock(ClientInterface::class);
    $store1 = $this->createMock(StoreInterface::class);
    $model1 = $this->createMock(AuthorizationModelInterface::class);
    
    $client2 = $this->createMock(ClientInterface::class);
    $store2 = $this->createMock(StoreInterface::class);
    $model2 = $this->createMock(AuthorizationModelInterface::class);
    
    Context::with(function () use ($client1, $store1, $model1, $client2, $store2, $model2) {
        expect(Context::getClient())->toBe($client1);
        expect(Context::getStore())->toBe($store1);
        expect(Context::getModel())->toBe($model1);
        
        Context::with(function () use ($client2, $store2, $model2) {
            expect(Context::getClient())->toBe($client2);
            expect(Context::getStore())->toBe($store2);
            expect(Context::getModel())->toBe($model2);
        }, client: $client2, store: $store2, model: $model2);
        
        // Verify parent context is restored
        expect(Context::getClient())->toBe($client1);
        expect(Context::getStore())->toBe($store1);
        expect(Context::getModel())->toBe($model1);
    }, client: $client1, store: $store1, model: $model1);
});

test('with returns callable result', function () {
    $result = Context::with(fn() => 'test-result');
    expect($result)->toBe('test-result');
});

test('with restores context after exception', function () {
    $client = $this->createMock(ClientInterface::class);
    
    Context::with(function () use ($client) {
        expect(Context::getClient())->toBe($client);
        
        try {
            Context::with(function () {
                throw new \Exception('Test exception');
            });
            fail('Exception should have been thrown');
        } catch (\Exception $e) {
            expect($e->getMessage())->toBe('Test exception');
        }
        
        // Verify parent context is restored after exception
        expect(Context::getClient())->toBe($client);
        expect(Context::depth())->toBe(1);
    }, client: $client);
    
    expect(Context::hasContext())->toBeFalse();
    expect(Context::depth())->toBe(0);
});