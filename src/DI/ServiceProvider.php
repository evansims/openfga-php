<?php

declare(strict_types=1);

namespace OpenFGA\DI;

use LogicException;
use OpenFGA\Authentication\AuthenticationInterface;
use OpenFGA\Events\{EventDispatcher, EventDispatcherInterface};
use OpenFGA\Events\{HttpRequestSentEvent, HttpResponseReceivedEvent, OperationCompletedEvent, OperationStartedEvent};
use OpenFGA\Network\RequestManager;
use OpenFGA\Observability\{NoOpTelemetryProvider, TelemetryEventListener, TelemetryInterface};
use OpenFGA\Repositories\{HttpAssertionRepository, HttpModelRepository, HttpStoreRepository, HttpTupleRepository};
use OpenFGA\Schemas\SchemaValidator;
use OpenFGA\Services\{AssertionService, AuthorizationService, ModelService, StoreService, TupleFilterService, TupleService};
use OpenFGA\Services\{AuthenticationService, EventAwareTelemetryService, HttpService, TelemetryServiceInterface};
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, ResponseFactoryInterface, StreamFactoryInterface};

use function is_int;
use function is_string;

/**
 * Service provider for managing OpenFGA service registration and configuration.
 *
 * This provider encapsulates the complex service dependency graph required
 * for the OpenFGA client, providing a clean interface for service registration
 * and retrieval. It manages the lifecycle of all services and ensures proper
 * dependency injection while maintaining performance through lazy loading.
 *
 * The provider supports configuration through various sources and provides
 * both default implementations and the ability to override services with
 * custom implementations for testing or specialized use cases.
 */
final class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array<string, mixed> Configuration parameters
     */
    private array $config = [];

    /**
     * @var array<string, callable(): ?object> Service factory functions
     */
    private array $factories = [];

    /**
     * @var array<string, object> Registered service instances
     */
    private array $services = [];

    /**
     * Create a new service provider with the specified configuration.
     *
     * @param string                       $url            The OpenFGA API URL
     * @param string|null                  $storeId        The default store ID (optional)
     * @param AuthenticationInterface|null $authentication Authentication provider (optional)
     * @param TelemetryInterface|null      $telemetry      Telemetry provider (optional)
     * @param array<string, mixed>         $options        Additional configuration options
     */
    public function __construct(
        string $url,
        ?string $storeId = null,
        ?AuthenticationInterface $authentication = null,
        ?TelemetryInterface $telemetry = null,
        array $options = [],
    ) {
        $this->config = [
            'url' => $url,
            'storeId' => $storeId,
            'authentication' => $authentication,
            'telemetry' => $telemetry ?? new NoOpTelemetryProvider,
            'language' => $options['language'] ?? 'en',
            'maxRetries' => $options['maxRetries'] ?? 3,
            'httpClient' => $options['httpClient'] ?? null,
            'requestFactory' => $options['requestFactory'] ?? null,
            'responseFactory' => $options['responseFactory'] ?? null,
            'streamFactory' => $options['streamFactory'] ?? null,
        ];

        $this->registerFactories();
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function factory(string $serviceId, callable $factory): void
    {
        $this->factories[$serviceId] = $factory;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function get(string $serviceId): ?object
    {
        if (isset($this->services[$serviceId])) {
            return $this->services[$serviceId];
        }

        if (! isset($this->factories[$serviceId])) {
            throw new ServiceNotFoundException($serviceId);
        }

        $service = $this->factories[$serviceId]();

        // Only cache non-null services
        if (null !== $service) {
            $this->services[$serviceId] = $service;
        }

        return $service;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function has(string $serviceId): bool
    {
        return isset($this->services[$serviceId]) || isset($this->factories[$serviceId]);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function set(string $serviceId, object $service): void
    {
        $this->services[$serviceId] = $service;
    }

    /**
     * Get authentication from configuration.
     */
    private function getConfigAuthentication(): ?AuthenticationInterface
    {
        /** @var mixed $value */
        $value = $this->config['authentication'] ?? null;

        if ($value instanceof AuthenticationInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get HTTP client from configuration.
     */
    private function getConfigHttpClient(): ?ClientInterface
    {
        /** @var mixed $value */
        $value = $this->config['httpClient'] ?? null;

        if ($value instanceof ClientInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get an integer value from configuration.
     *
     * @param string $key
     */
    private function getConfigInt(string $key): ?int
    {
        /** @var mixed $value */
        $value = $this->config[$key] ?? null;

        if (is_int($value)) {
            return $value;
        }

        return null;
    }

    /**
     * Get request factory from configuration.
     */
    private function getConfigRequestFactory(): ?RequestFactoryInterface
    {
        /** @var mixed $value */
        $value = $this->config['requestFactory'] ?? null;

        if ($value instanceof RequestFactoryInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get response factory from configuration.
     */
    private function getConfigResponseFactory(): ?ResponseFactoryInterface
    {
        /** @var mixed $value */
        $value = $this->config['responseFactory'] ?? null;

        if ($value instanceof ResponseFactoryInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get stream factory from configuration.
     */
    private function getConfigStreamFactory(): ?StreamFactoryInterface
    {
        /** @var mixed $value */
        $value = $this->config['streamFactory'] ?? null;

        if ($value instanceof StreamFactoryInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get a string value from configuration with a default fallback.
     *
     * @param string $key
     * @param string $default
     */
    private function getConfigString(string $key, string $default = ''): string
    {
        /** @var mixed $value */
        $value = $this->config[$key] ?? null;

        if (is_string($value)) {
            return $value;
        }

        return $default;
    }

    /**
     * Get telemetry interface from configuration.
     */
    private function getConfigTelemetryInterface(): ?TelemetryInterface
    {
        /** @var mixed $value */
        $value = $this->config['telemetry'] ?? null;

        if ($value instanceof TelemetryInterface) {
            return $value;
        }

        return null;
    }

    /**
     * Get the telemetry provider from configuration or provide a default.
     */
    private function getTelemetryProvider(): TelemetryInterface
    {
        /** @var mixed|TelemetryInterface|null $telemetry */
        $telemetry = $this->config['telemetry'] ?? null;

        if ($telemetry instanceof TelemetryInterface) {
            return $telemetry;
        }

        return new NoOpTelemetryProvider;
    }

    /**
     * Register all service factories with the provider.
     */
    private function registerFactories(): void
    {
        // Event system
        $this->factory('event.dispatcher', static fn (): EventDispatcherInterface => new EventDispatcher);

        $this->factory('telemetry.listener', fn (): TelemetryEventListener => new TelemetryEventListener(
            $this->getTelemetryProvider(),
        ));

        // Infrastructure services - removed ConfigurationService

        $this->factory('telemetry', function (): TelemetryServiceInterface {
            $eventDispatcher = $this->get('event.dispatcher');
            $telemetryListener = $this->get('telemetry.listener');

            // Ensure we have valid instances
            if ($eventDispatcher instanceof EventDispatcherInterface && $telemetryListener instanceof TelemetryEventListener) {
                // Register telemetry event listeners
                $eventDispatcher->addListener(
                    'OpenFGA\Events\HttpRequestSentEvent',
                    static function (object $event) use ($telemetryListener): void {
                        if ($event instanceof HttpRequestSentEvent) {
                            $telemetryListener->onHttpRequestSent($event);
                        }
                    },
                );
                $eventDispatcher->addListener(
                    'OpenFGA\Events\HttpResponseReceivedEvent',
                    static function (object $event) use ($telemetryListener): void {
                        if ($event instanceof HttpResponseReceivedEvent) {
                            $telemetryListener->onHttpResponseReceived($event);
                        }
                    },
                );
                $eventDispatcher->addListener(
                    'OpenFGA\Events\OperationStartedEvent',
                    static function (object $event) use ($telemetryListener): void {
                        if ($event instanceof OperationStartedEvent) {
                            $telemetryListener->onOperationStarted($event);
                        }
                    },
                );
                $eventDispatcher->addListener(
                    'OpenFGA\Events\OperationCompletedEvent',
                    static function (object $event) use ($telemetryListener): void {
                        if ($event instanceof OperationCompletedEvent) {
                            $telemetryListener->onOperationCompleted($event);
                        }
                    },
                );
            }

            return new EventAwareTelemetryService(
                $this->getConfigTelemetryInterface() ?? new NoOpTelemetryProvider,
                $eventDispatcher instanceof EventDispatcherInterface ? $eventDispatcher : null,
            );
        });

        $this->factory('authentication', function (): AuthenticationService {
            $telemetryService = $this->get('telemetry');

            return new AuthenticationService(
                $this->getConfigAuthentication(),
                $telemetryService instanceof TelemetryServiceInterface ? $telemetryService : null,
            );
        });

        $this->factory('http', function (): HttpService {
            $eventDispatcher = $this->get('event.dispatcher');

            $requestManager = new RequestManager(
                url: $this->getConfigString('url', ''),
                maxRetries: max(0, min($this->getConfigInt('maxRetries') ?? 3, 15)),
                authorizationHeader: null, // Authorization header will be set later
                httpClient: $this->getConfigHttpClient(),
                httpStreamFactory: $this->getConfigStreamFactory(),
                httpRequestFactory: $this->getConfigRequestFactory(),
                httpResponseFactory: $this->getConfigResponseFactory(),
                telemetry: $this->getConfigTelemetryInterface(),
            );

            return new HttpService(
                $requestManager,
                $eventDispatcher instanceof EventDispatcherInterface ? $eventDispatcher : null,
            );
        });

        // Domain services
        $this->factory('schema.validator', static fn (): SchemaValidator => new SchemaValidator);

        $this->factory('tuple.filter', static fn (): TupleFilterService => new TupleFilterService);

        // Repository layer
        $this->factory('repository.store', function (): HttpStoreRepository {
            $httpService = $this->get('http');
            $validator = $this->get('schema.validator');

            if (! $httpService instanceof HttpService) {
                throw new LogicException('HTTP service not available');
            }

            if (! $validator instanceof SchemaValidator) {
                throw new LogicException('Schema validator not available');
            }

            return new HttpStoreRepository($httpService, $validator);
        });

        $this->factory(
            'repository.model',
            function (): ?HttpModelRepository {
                $storeId = $this->config['storeId'] ?? null;

                if (! is_string($storeId) || '' === $storeId) {
                    return null;
                }

                $httpService = $this->get('http');
                $validator = $this->get('schema.validator');

                if (! $httpService instanceof HttpService) {
                    throw new LogicException('HTTP service not available');
                }

                if (! $validator instanceof SchemaValidator) {
                    throw new LogicException('Schema validator not available');
                }

                return new HttpModelRepository($httpService, $validator, $storeId);
            },
        );

        $this->factory('repository.tuple', function (): HttpTupleRepository {
            $httpService = $this->get('http');
            $tupleFilterService = $this->get('tuple.filter');
            $validator = $this->get('schema.validator');

            if (! $httpService instanceof HttpService) {
                throw new LogicException('HTTP service not available');
            }

            if (! $tupleFilterService instanceof TupleFilterService) {
                throw new LogicException('Tuple filter service not available');
            }

            if (! $validator instanceof SchemaValidator) {
                throw new LogicException('Schema validator not available');
            }

            return new HttpTupleRepository($httpService, $tupleFilterService, $validator);
        });

        $this->factory(
            'repository.assertion',
            function (): ?HttpAssertionRepository {
                $storeId = $this->config['storeId'] ?? null;

                if (! is_string($storeId) || '' === $storeId) {
                    return null;
                }

                $httpService = $this->get('http');
                $validator = $this->get('schema.validator');

                if (! $httpService instanceof HttpService) {
                    throw new LogicException('HTTP service not available');
                }

                if (! $validator instanceof SchemaValidator) {
                    throw new LogicException('Schema validator not available');
                }

                return new HttpAssertionRepository($httpService, $validator, $storeId);
            },
        );

        // Application services
        $this->factory('service.authorization', function (): AuthorizationService {
            $httpService = $this->get('http');

            if (! $httpService instanceof HttpService) {
                throw new LogicException('HTTP service not available');
            }

            return new AuthorizationService($httpService);
        });

        $this->factory('service.store', function (): StoreService {
            $storeRepository = $this->get('repository.store');

            if (! $storeRepository instanceof HttpStoreRepository) {
                throw new LogicException('Store repository not available');
            }

            return new StoreService($storeRepository);
        });

        $this->factory(
            'service.model',
            function (): ?ModelService {
                $modelRepository = $this->get('repository.model');
                $httpService = $this->get('http');
                $validator = $this->get('schema.validator');

                if (! ($modelRepository instanceof HttpModelRepository)) {
                    return null;
                }

                if (! $httpService instanceof HttpService) {
                    throw new LogicException('HTTP service not available');
                }

                if (! $validator instanceof SchemaValidator) {
                    throw new LogicException('Schema validator not available');
                }

                return new ModelService(
                    $modelRepository,
                    $httpService,
                    $validator,
                    $this->getConfigString('language', 'en'),
                );
            },
        );

        $this->factory('service.tuple', function (): TupleService {
            $tupleRepository = $this->get('repository.tuple');

            if (! $tupleRepository instanceof HttpTupleRepository) {
                throw new LogicException('Tuple repository not available');
            }

            return new TupleService($tupleRepository);
        });

        $this->factory(
            'service.assertion',
            function (): ?AssertionService {
                $assertionRepository = $this->get('repository.assertion');

                return $assertionRepository instanceof HttpAssertionRepository
                    ? new AssertionService($assertionRepository, $this->getConfigString('language', 'en'))
                    : null;
            },
        );
    }
}
