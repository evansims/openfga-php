<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\DI;

use OpenFGA\Authentication\TokenAuthentication;
use OpenFGA\{Configuration, ConfigurationInterface, ServiceNotFoundException};
use OpenFGA\Events\{EventDispatcher};
use OpenFGA\Network\{ExponentialBackoffRetryStrategy, PsrHttpClient};
use OpenFGA\Network\RequestManager;
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryEventListener};
use OpenFGA\Repositories\{HttpAssertionRepository, HttpModelRepository, HttpStoreRepository, HttpTupleRepository};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\{AssertionService, AuthorizationService, ModelService, StoreService, TupleFilterService, TupleService};
use OpenFGA\Services\{AuthenticationService, EventAwareTelemetryService, HttpService, TelemetryServiceInterface};
use PHPUnit\Framework\Attributes\{CoversClass, UsesClass};
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Unit tests for Configuration.
 *
 * Tests the dependency injection configuration provider implementation,
 * verifying service registration, retrieval, and factory functionality.
 */
#[CoversClass(Configuration::class)]
#[UsesClass(ServiceNotFoundException::class)]
#[UsesClass(EventDispatcher::class)]
#[UsesClass(TelemetryEventListener::class)]
#[UsesClass(AuthenticationService::class)]
#[UsesClass(EventAwareTelemetryService::class)]
#[UsesClass(HttpService::class)]
#[UsesClass(SchemaValidator::class)]
#[UsesClass(TupleFilterService::class)]
#[UsesClass(HttpStoreRepository::class)]
#[UsesClass(HttpModelRepository::class)]
#[UsesClass(HttpTupleRepository::class)]
#[UsesClass(HttpAssertionRepository::class)]
#[UsesClass(AuthorizationService::class)]
#[UsesClass(StoreService::class)]
#[UsesClass(ModelService::class)]
#[UsesClass(TupleService::class)]
#[UsesClass(AssertionService::class)]
#[UsesClass(TokenAuthentication::class)]
#[UsesClass(RequestManager::class)]
#[UsesClass(ExponentialBackoffRetryStrategy::class)]
#[UsesClass(PsrHttpClient::class)]
final class ServiceProviderTest extends TestCase
{
    private Configuration $provider;

    protected function setUp(): void
    {
        $this->provider = new Configuration('https://api.fga.example');
    }

    public function testAllCoreServicesAreRegistered(): void
    {
        $expectedServices = [
            'telemetry',
            'authentication',
            'http',
            'schema.validator',
            'tuple.filter',
            'repository.store',
            'repository.model',
            'repository.tuple',
            'repository.assertion',
            'service.authorization',
            'service.store',
            'service.model',
            'service.tuple',
            'service.assertion',
        ];

        foreach ($expectedServices as $serviceId) {
            $this->assertTrue($this->provider->has($serviceId), "Service '{$serviceId}' should be registered");
        }
    }

    public function testCanRegisterCustomService(): void
    {
        $customService = new stdClass;
        $this->provider->set('custom', $customService);

        $this->assertTrue($this->provider->has('custom'));
        $this->assertSame($customService, $this->provider->get('custom'));
    }

    public function testCanRegisterServiceFactory(): void
    {
        $this->provider->factory('test', fn () => new stdClass);

        $this->assertTrue($this->provider->has('test'));

        $service1 = $this->provider->get('test');
        $service2 = $this->provider->get('test');

        $this->assertInstanceOf(stdClass::class, $service1);
        $this->assertSame($service1, $service2); // Should be same instance (singleton)
    }

    public function testConstructorWithAuthentication(): void
    {
        $auth = new TokenAuthentication('test-token');
        $provider = new Configuration('https://api.fga.example', null, $auth);

        $authService = $provider->get('authentication');
        $this->assertInstanceOf(AuthenticationService::class, $authService);
    }

    public function testConstructorWithOptions(): void
    {
        $provider = new Configuration(
            url: 'https://api.fga.example',
            storeId: null,
            authentication: null,
            telemetry: null,
            language: 'es',
            maxRetries: 5,
        );

        // Service should be created with the provided options
        $this->assertTrue($provider->has('telemetry'));
        $this->assertTrue($provider->has('service.assertion'));
    }

    public function testConstructorWithStoreId(): void
    {
        $provider = new Configuration('https://api.fga.example', '01ARZ3NDEKTSV4RRFFQ69G5FAV');

        $this->assertTrue($provider->has('repository.model'));
        $this->assertTrue($provider->has('repository.assertion'));
    }

    public function testConstructorWithTelemetry(): void
    {
        $telemetry = new NoOpTelemetryProvider;
        $provider = new Configuration('https://api.fga.example', null, null, $telemetry);

        $telemetryService = $provider->get('telemetry');
        $this->assertInstanceOf(TelemetryServiceInterface::class, $telemetryService);
        $this->assertInstanceOf(EventAwareTelemetryService::class, $telemetryService);
    }

    public function testGetReturnsSameInstanceOnSubsequentCalls(): void
    {
        $http1 = $this->provider->get('http');
        $http2 = $this->provider->get('http');

        $this->assertSame($http1, $http2);
    }

    public function testGetReturnsServiceInstance(): void
    {
        $http = $this->provider->get('http');
        $this->assertInstanceOf(HttpService::class, $http);
    }

    public function testGetThrowsExceptionForUnregisteredService(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->expectExceptionMessage('Service "nonexistent" not found. Please ensure the service is registered with the configuration provider.');

        $this->provider->get('nonexistent');
    }

    public function testHasReturnsFalseForUnregisteredServices(): void
    {
        $this->assertFalse($this->provider->has('nonexistent'));
        $this->assertFalse($this->provider->has('invalid.service'));
    }

    public function testHasReturnsTrueForRegisteredServices(): void
    {
        $this->assertTrue($this->provider->has('telemetry'));
        $this->assertTrue($this->provider->has('http'));
        $this->assertTrue($this->provider->has('repository.store'));
        $this->assertTrue($this->provider->has('service.authorization'));
    }

    public function testImplementsConfigurationInterface(): void
    {
        $this->assertInstanceOf(ConfigurationInterface::class, $this->provider);
    }

    public function testServiceDependenciesWork(): void
    {
        // Test that services can depend on each other
        $httpService = $this->provider->get('http');
        $this->assertInstanceOf(HttpService::class, $httpService);

        $storeRepo = $this->provider->get('repository.store');
        $this->assertInstanceOf(HttpStoreRepository::class, $storeRepo);

        $storeService = $this->provider->get('service.store');
        $this->assertInstanceOf(StoreService::class, $storeService);
    }
}
