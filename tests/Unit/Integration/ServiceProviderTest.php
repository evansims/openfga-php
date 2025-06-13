<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Integration;

use ArrayAccess;
use Exception;
use OpenFGA\{ClientInterface, TransformerInterface};
use OpenFGA\Integration\ServiceProvider;
use OpenFGA\Network\RequestManagerInterface;
use OpenFGA\Observability\{TelemetryInterface};
use OpenFGA\Schemas\{SchemaValidator, SchemaValidatorInterface};
use OpenFGA\Transformer;

describe('ServiceProvider', function (): void {
    beforeEach(function (): void {
        $this->serviceProvider = new ServiceProvider;
        $this->container = new class {
            private array $services = [];

            public function get(string $id): mixed
            {
                if (! isset($this->services[$id])) {
                    throw new Exception("Service {$id} not found");
                }

                $factory = $this->services[$id];

                return $factory($this);
            }

            public function has(string $id): bool
            {
                return isset($this->services[$id]);
            }

            public function set(string $id, callable $factory): void
            {
                $this->services[$id] = $factory;
            }
        };
    });

    it('registers configuration-free OpenFGA services', function (): void {
        $this->serviceProvider->register($this->container);

        expect($this->container->has(TelemetryInterface::class))->toBe(true);
        expect($this->container->has(TransformerInterface::class))->toBe(true);
        expect($this->container->has(SchemaValidatorInterface::class))->toBe(true);

        // These require configuration and should NOT be registered automatically
        expect($this->container->has(ClientInterface::class))->toBe(false);
        expect($this->container->has(RequestManagerInterface::class))->toBe(false);
    });

    it('creates working instances of registered services', function (): void {
        $this->serviceProvider->register($this->container);

        $telemetry = $this->container->get(TelemetryInterface::class);
        $transformer = $this->container->get(TransformerInterface::class);
        $schemaValidator = $this->container->get(SchemaValidatorInterface::class);

        expect(null === $telemetry || $telemetry instanceof TelemetryInterface)->toBeTrue();
        expect($transformer)->toBeInstanceOf(TransformerInterface::class);
        expect($schemaValidator)->toBeInstanceOf(SchemaValidatorInterface::class);
    });

    it('works with different container interface styles', function (): void {
        // Test with bind() method
        $bindContainer = new class {
            private array $services = [];

            public function bind(string $id, callable $factory): void
            {
                $this->services[$id] = $factory;
            }

            public function get(string $id): mixed
            {
                return $this->services[$id]($this);
            }

            public function has(string $id): bool
            {
                return isset($this->services[$id]);
            }
        };

        $this->serviceProvider->register($bindContainer);
        expect($bindContainer->has(TelemetryInterface::class))->toBe(true);

        // Test with singleton() method
        $singletonContainer = new class {
            private array $services = [];

            public function get(string $id): mixed
            {
                return $this->services[$id]($this);
            }

            public function has(string $id): bool
            {
                return isset($this->services[$id]);
            }

            public function singleton(string $id, callable $factory): void
            {
                $this->services[$id] = $factory;
            }
        };

        $this->serviceProvider->register($singletonContainer);
        expect($singletonContainer->has(TelemetryInterface::class))->toBe(true);

        // Test with register() method
        $registerContainer = new class {
            private array $services = [];

            public function get(string $id): mixed
            {
                return $this->services[$id]($this);
            }

            public function has(string $id): bool
            {
                return isset($this->services[$id]);
            }

            public function register(string $id, callable $factory): void
            {
                $this->services[$id] = $factory;
            }
        };

        $this->serviceProvider->register($registerContainer);
        expect($registerContainer->has(TelemetryInterface::class))->toBe(true);
    });

    it('works with ArrayAccess containers', function (): void {
        $arrayContainer = new class implements ArrayAccess {
            private array $services = [];

            public function offsetExists(mixed $offset): bool
            {
                return isset($this->services[$offset]);
            }

            public function offsetGet(mixed $offset): mixed
            {
                return $this->services[$offset];
            }

            public function offsetSet(mixed $offset, mixed $value): void
            {
                $this->services[$offset] = $value;
            }

            public function offsetUnset(mixed $offset): void
            {
                unset($this->services[$offset]);
            }
        };

        $this->serviceProvider->register($arrayContainer);
        expect($arrayContainer->offsetExists(TelemetryInterface::class))->toBe(true);
    });

    it('gracefully handles unsupported containers', function (): void {
        $unsupportedContainer = new class {
            // No supported methods
        };

        // Should not throw an exception
        expect(fn () => $this->serviceProvider->register($unsupportedContainer))
            ->not->toThrow(Exception::class);
    });

    it('creates null telemetry by default', function (): void {
        $this->serviceProvider->register($this->container);

        $telemetry = $this->container->get(TelemetryInterface::class);

        // Should be null (no telemetry)
        expect($telemetry)->toBeNull();
    });

    it('creates working transformer', function (): void {
        $this->serviceProvider->register($this->container);

        $transformer = $this->container->get(TransformerInterface::class);

        expect($transformer)->toBeInstanceOf(TransformerInterface::class);
        expect($transformer::class)->toBe(Transformer::class);
    });

    it('creates working schema validator', function (): void {
        $this->serviceProvider->register($this->container);

        $validator = $this->container->get(SchemaValidatorInterface::class);

        expect($validator)->toBeInstanceOf(SchemaValidatorInterface::class);
        expect($validator::class)->toBe(SchemaValidator::class);
    });
});
