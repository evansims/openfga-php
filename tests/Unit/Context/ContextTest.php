<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Context;

use OpenFGA\ClientInterface;
use OpenFGA\Context\Context;
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\StoreInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(Context::class)]
final class ContextTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the context state before each test
        while (Context::hasContext()) {
            Context::with(fn() => null);
        }
    }

    #[Test]
    public function testHasContextReturnsFalseWhenNoContextIsActive(): void
    {
        $this->assertFalse(Context::hasContext());
    }

    #[Test]
    public function testHasContextReturnsTrueWhenContextIsActive(): void
    {
        Context::with(function () {
            $this->assertTrue(Context::hasContext());
        });
    }

    #[Test]
    public function testCurrentThrowsExceptionWhenNoContextIsActive(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No context is currently active. Wrap your code in Context::with() or use the context helper method.');
        
        Context::current();
    }

    #[Test]
    public function testDepthReturnsZeroWhenNoContextIsActive(): void
    {
        $this->assertSame(0, Context::depth());
    }

    #[Test]
    public function testDepthIncreasesWithNestedContexts(): void
    {
        Context::with(function () {
            $this->assertSame(1, Context::depth());
            
            Context::with(function () {
                $this->assertSame(2, Context::depth());
                
                Context::with(function () {
                    $this->assertSame(3, Context::depth());
                });
                
                $this->assertSame(2, Context::depth());
            });
            
            $this->assertSame(1, Context::depth());
        });
        
        $this->assertSame(0, Context::depth());
    }

    #[Test]
    public function testGetClientReturnsNullWhenNoContextIsActive(): void
    {
        $this->assertNull(Context::getClient());
    }

    #[Test]
    public function testGetClientReturnsClientFromContext(): void
    {
        $client = $this->createMock(ClientInterface::class);
        
        Context::with(function () use ($client) {
            $this->assertSame($client, Context::getClient());
        }, client: $client);
    }

    #[Test]
    public function testGetStoreReturnsNullWhenNoContextIsActive(): void
    {
        $this->assertNull(Context::getStore());
    }

    #[Test]
    public function testGetStoreReturnsStoreFromContext(): void
    {
        $store = $this->createMock(StoreInterface::class);
        
        Context::with(function () use ($store) {
            $this->assertSame($store, Context::getStore());
        }, store: $store);
    }

    #[Test]
    public function testGetStoreReturnsStringStoreFromContext(): void
    {
        $store = 'store-id';
        
        Context::with(function () use ($store) {
            $this->assertSame($store, Context::getStore());
        }, store: $store);
    }

    #[Test]
    public function testGetModelReturnsNullWhenNoContextIsActive(): void
    {
        $this->assertNull(Context::getModel());
    }

    #[Test]
    public function testGetModelReturnsModelFromContext(): void
    {
        $model = $this->createMock(AuthorizationModelInterface::class);
        
        Context::with(function () use ($model) {
            $this->assertSame($model, Context::getModel());
        }, model: $model);
    }

    #[Test]
    public function testGetModelReturnsStringModelFromContext(): void
    {
        $model = 'model-id';
        
        Context::with(function () use ($model) {
            $this->assertSame($model, Context::getModel());
        }, model: $model);
    }

    #[Test]
    public function testGetPreviousReturnsNullWhenNoContextIsActive(): void
    {
        $this->assertNull(Context::getPrevious());
    }

    #[Test]
    public function testGetPreviousReturnsNullForTopLevelContext(): void
    {
        Context::with(function () {
            $this->assertNull(Context::getPrevious());
        });
    }

    #[Test]
    public function testNestedContextInheritsValuesFromParent(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $store = $this->createMock(StoreInterface::class);
        $model = $this->createMock(AuthorizationModelInterface::class);
        
        Context::with(function () use ($client, $store, $model) {
            $this->assertSame($client, Context::getClient());
            $this->assertSame($store, Context::getStore());
            $this->assertSame($model, Context::getModel());
            
            Context::with(function () use ($client, $store, $model) {
                $this->assertSame($client, Context::getClient());
                $this->assertSame($store, Context::getStore());
                $this->assertSame($model, Context::getModel());
            });
        }, client: $client, store: $store, model: $model);
    }

    #[Test]
    public function testNestedContextCanOverrideParentValues(): void
    {
        $client1 = $this->createMock(ClientInterface::class);
        $store1 = $this->createMock(StoreInterface::class);
        $model1 = $this->createMock(AuthorizationModelInterface::class);
        
        $client2 = $this->createMock(ClientInterface::class);
        $store2 = $this->createMock(StoreInterface::class);
        $model2 = $this->createMock(AuthorizationModelInterface::class);
        
        Context::with(function () use ($client1, $store1, $model1, $client2, $store2, $model2) {
            $this->assertSame($client1, Context::getClient());
            $this->assertSame($store1, Context::getStore());
            $this->assertSame($model1, Context::getModel());
            
            Context::with(function () use ($client2, $store2, $model2) {
                $this->assertSame($client2, Context::getClient());
                $this->assertSame($store2, Context::getStore());
                $this->assertSame($model2, Context::getModel());
            }, client: $client2, store: $store2, model: $model2);
            
            // Verify parent context is restored
            $this->assertSame($client1, Context::getClient());
            $this->assertSame($store1, Context::getStore());
            $this->assertSame($model1, Context::getModel());
        }, client: $client1, store: $store1, model: $model1);
    }

    #[Test]
    public function testWithReturnsCallableResult(): void
    {
        $result = Context::with(fn() => 'test-result');
        $this->assertSame('test-result', $result);
    }

    #[Test]
    public function testWithRestoresContextAfterException(): void
    {
        $client = $this->createMock(ClientInterface::class);
        
        Context::with(function () use ($client) {
            $this->assertSame($client, Context::getClient());
            
            try {
                Context::with(function () {
                    throw new \Exception('Test exception');
                });
                $this->fail('Exception should have been thrown');
            } catch (\Exception $e) {
                $this->assertSame('Test exception', $e->getMessage());
            }
            
            // Verify parent context is restored after exception
            $this->assertSame($client, Context::getClient());
            $this->assertSame(1, Context::depth());
        }, client: $client);
        
        $this->assertFalse(Context::hasContext());
        $this->assertSame(0, Context::depth());
    }
}