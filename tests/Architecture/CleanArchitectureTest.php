<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Architecture;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function in_array;

/**
 * Architectural tests to enforce clean architecture boundaries.
 *
 * These tests ensure that dependencies only flow inward according to
 * clean architecture principles:
 * - Domain has no external dependencies
 * - Application depends only on Domain
 * - Infrastructure depends on Domain and Application
 * - Presentation depends on all inner layers
 */
final class CleanArchitectureTest extends TestCase
{
    private const SRC_PATH = __DIR__ . '/../../src';

    /**
     * @var array<string, array<string>>
     */
    private array $allowedDependencies = [
        'Domain' => [],
        'Application' => ['Domain'],
        'Infrastructure' => ['Domain', 'Application'],
        'Presentation' => ['Domain', 'Application', 'Infrastructure'],
    ];

    /**
     * @var array<string, array<string, array<string>>>
     */
    private array $exceptions = [
        // Allow Translator in Domain for error messages
        'Domain' => [
            'Infrastructure' => [
                'OpenFGA\Infrastructure\Translation\Translator',
            ],
            'Presentation' => [
                'OpenFGA\Messages',  // Allow Messages for error contexts
            ],
        ],
        // Allow Infrastructure services in Application for dependency injection
        'Application' => [
            'Infrastructure' => [
                'OpenFGA\Infrastructure\Services\HttpServiceInterface',
                'OpenFGA\Infrastructure\Translation\Translator',
                'OpenFGA\Network\RetryHandlerInterface',
                'OpenFGA\Network\HttpClientInterface',
                'OpenFGA\Authentication\AuthenticationInterface',
                // Allow Network components in Application Http layer
                'OpenFGA\Network\CircuitBreaker',
                'OpenFGA\Network\CircuitBreakerInterface',
                'OpenFGA\Network\ConcurrentExecutorInterface',
                'OpenFGA\Network\ExponentialBackoffRetryStrategy',
                'OpenFGA\Network\FiberConcurrentExecutor',
                'OpenFGA\Network\PsrHttpClient',
                'OpenFGA\Network\RetryStrategyInterface',
                'OpenFGA\Network\ParallelTaskExecutor',
                // Allow Infrastructure services for dependency injection
                'OpenFGA\Infrastructure\Repositories\HttpAssertionRepository',
                'OpenFGA\Infrastructure\Repositories\HttpModelRepository',
                'OpenFGA\Infrastructure\Repositories\HttpStoreRepository',
                'OpenFGA\Infrastructure\Repositories\HttpTupleRepository',
                'OpenFGA\Infrastructure\Services\AuthenticationService',
                'OpenFGA\Infrastructure\Services\ConfigurationService',
                'OpenFGA\Infrastructure\Services\EventAwareTelemetryService',
                'OpenFGA\Infrastructure\Services\HttpService',
                'OpenFGA\Infrastructure\Services\TelemetryService',
                'OpenFGA\Infrastructure\Services\TelemetryServiceInterface',
                'OpenFGA\Infrastructure\Telemetry\TelemetryEventListener',
            ],
            'Presentation' => [
                'OpenFGA\Client',        // Allow Client reference for typing
                'OpenFGA\ClientInterface', // Allow ClientInterface reference for factory
            ],
        ],
        // Allow Client in Infrastructure for telemetry
        'Infrastructure' => [
            'Presentation' => [
                'OpenFGA\Client',  // For telemetry provider
            ],
        ],
    ];

    /**
     * @var array<string, array<string>>
     */
    private array $layerDefinitions = [
        'Domain' => [
            'Models/',
            'Domain/',
        ],
        'Application' => [
            'Application/',
        ],
        'Infrastructure' => [
            'Infrastructure/',
            'Network/',
            'Authentication/',
            'Observability/',
        ],
        'Presentation' => [
            'Client.php',
            'ClientInterface.php',
            'Helpers.php',
            'Messages.php',
        ],
    ];

    public function testApplicationLayerOnlyDependsOnDomain(): void
    {
        $violations = $this->findLayerViolations('Application');

        $this->assertEmpty(
            $violations,
            "Application layer should only depend on Domain layer.\nViolations found:\n" .
            $this->formatViolations($violations),
        );
    }

    public function testDomainLayerHasNoDependencies(): void
    {
        $violations = $this->findLayerViolations('Domain');

        $this->assertEmpty(
            $violations,
            "Domain layer should have no dependencies on other layers.\nViolations found:\n" .
            $this->formatViolations($violations),
        );
    }

    public function testInfrastructureLayerOnlyDependsOnDomainAndApplication(): void
    {
        $violations = $this->findLayerViolations('Infrastructure');

        $this->assertEmpty(
            $violations,
            "Infrastructure layer should only depend on Domain and Application layers.\nViolations found:\n" .
            $this->formatViolations($violations),
        );
    }

    /**
     * Test that repository implementations are in Infrastructure layer.
     */
    public function testRepositoryImplementationsAreInInfrastructureLayer(): void
    {
        $repositoryImplementations = [
            'HttpAssertionRepository',
            'HttpModelRepository',
            'HttpStoreRepository',
            'HttpTupleRepository',
        ];

        foreach ($repositoryImplementations as $implementation) {
            $file = self::SRC_PATH . '/Infrastructure/Repositories/' . $implementation . '.php';

            $this->assertFileExists(
                $file,
                "Repository implementation {$implementation} should be in Infrastructure/Repositories/",
            );
        }
    }

    /**
     * Test that repository interfaces are in Application layer.
     */
    public function testRepositoryInterfacesAreInApplicationLayer(): void
    {
        $repositoryInterfaces = [
            'AssertionRepositoryInterface',
            'ModelRepositoryInterface',
            'StoreRepositoryInterface',
            'TupleRepositoryInterface',
        ];

        foreach ($repositoryInterfaces as $interface) {
            $file = self::SRC_PATH . '/Application/Repositories/' . $interface . '.php';

            $this->assertFileExists(
                $file,
                "Repository interface {$interface} should be in Application/Repositories/",
            );
        }
    }

    /**
     * Test that service interfaces are properly organized.
     */
    public function testServiceInterfacesAreProperlyOrganized(): void
    {
        // Application services
        $applicationServices = [
            'AssertionServiceInterface',
            'AuthorizationServiceInterface',
            'ModelServiceInterface',
            'StoreServiceInterface',
            'TupleFilterServiceInterface',
            'TupleServiceInterface',
        ];

        foreach ($applicationServices as $service) {
            $file = self::SRC_PATH . '/Application/Services/' . $service . '.php';

            $this->assertFileExists(
                $file,
                "Application service interface {$service} should be in Application/Services/",
            );
        }

        // Infrastructure services
        $infrastructureServices = [
            'AuthenticationServiceInterface',
            'ConfigurationServiceInterface',
            'HttpServiceInterface',
            'TelemetryServiceInterface',
        ];

        foreach ($infrastructureServices as $service) {
            $file = self::SRC_PATH . '/Infrastructure/Services/' . $service . '.php';

            $this->assertFileExists(
                $file,
                "Infrastructure service interface {$service} should be in Infrastructure/Services/",
            );
        }

        // Domain services (moved to proper layer)
        $domainServices = [
            'ValidationServiceInterface',
        ];

        foreach ($domainServices as $service) {
            $file = self::SRC_PATH . '/Domain/Schema/' . $service . '.php';

            $this->assertFileExists(
                $file,
                "Domain service interface {$service} should be in Domain/Schema/",
            );
        }
    }

    /**
     * @param  string        $file
     * @return array<string>
     */
    private function extractDependencies(string $file): array
    {
        $content = file_get_contents($file);
        $dependencies = [];

        // Extract use statements - handle both single and grouped imports
        preg_match_all('/use\s+([^;]+);/', $content, $matches);

        foreach ($matches[1] as $match) {
            // Clean up the match
            $match = trim($match);

            // Handle grouped imports like: use OpenFGA\Exceptions\{ClientError, ClientThrowable}
            if (preg_match('/^(.+?)\s*\{(.+)\}/', $match, $groupMatches)) {
                $baseNamespace = trim($groupMatches[1], '\\ ');
                $classes = explode(',', $groupMatches[2]);

                foreach ($classes as $class) {
                    $class = trim($class);

                    // Skip aliases and empty entries
                    if (! empty($class) && ! strpos($class, ' as ')) {
                        $fullClass = $baseNamespace . '\\' . $class;

                        if (str_starts_with($fullClass, 'OpenFGA\\')) {
                            $dependencies[] = $fullClass;
                        }
                    }
                }
            } else {
                // Single import - skip if it has an alias or is not OpenFGA
                if (! strpos($match, ' as ') && str_starts_with($match, 'OpenFGA\\')) {
                    $dependencies[] = $match;
                }
            }
        }

        // Remove duplicates
        return array_unique($dependencies);
    }

    /**
     * @param  string                                      $layer
     * @return array<string, array<string, array<string>>>
     */
    private function findLayerViolations(string $layer): array
    {
        $violations = [];
        $files = $this->getFilesInLayer($layer);

        foreach ($files as $file) {
            $namespace = $this->getNamespaceFromFile($file);

            if (null === $namespace) {
                continue;
            }

            $dependencies = $this->extractDependencies($file);

            foreach ($dependencies as $dependency) {
                $dependencyLayer = $this->getLayerForNamespace($dependency);

                if (null === $dependencyLayer || $dependencyLayer === $layer) {
                    continue;
                }

                if (! in_array($dependencyLayer, $this->allowedDependencies[$layer], true)) {
                    // Check if this is an allowed exception
                    if ($this->isAllowedException($layer, $dependencyLayer, $dependency)) {
                        continue;
                    }

                    $violations[$namespace][$dependencyLayer][] = $dependency;
                }
            }
        }

        return $violations;
    }

    /**
     * @param array<string, array<string, array<string>>> $violations
     */
    private function formatViolations(array $violations): string
    {
        $output = [];

        foreach ($violations as $class => $layerViolations) {
            $output[] = "\n{$class}:";

            foreach ($layerViolations as $layer => $dependencies) {
                $output[] = "  Depends on {$layer} layer:";

                foreach ($dependencies as $dependency) {
                    $output[] = "    - {$dependency}";
                }
            }
        }

        return implode("\n", $output);
    }

    /**
     * @param  string        $layer
     * @return array<string>
     */
    private function getFilesInLayer(string $layer): array
    {
        $files = [];

        foreach ($this->layerDefinitions[$layer] as $path) {
            $fullPath = self::SRC_PATH . '/' . $path;

            if (is_file($fullPath)) {
                $files[] = $fullPath;
            } elseif (is_dir($fullPath)) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($fullPath),
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && 'php' === $file->getExtension()) {
                        $files[] = $file->getPathname();
                    }
                }
            }
        }

        return $files;
    }

    private function getLayerForNamespace(string $namespace): ?string
    {
        // Remove OpenFGA prefix
        $relativePath = str_replace('OpenFGA\\', '', $namespace);

        // Check each layer
        foreach ($this->layerDefinitions as $layer => $paths) {
            foreach ($paths as $path) {
                $path = str_replace('/', '\\', rtrim($path, '/'));

                if (str_starts_with($relativePath, $path)) {
                    return $layer;
                }
            }
        }

        // Special cases
        if ('Client' === $relativePath || 'ClientInterface' === $relativePath) {
            return 'Presentation';
        }

        return null;
    }

    private function getNamespaceFromFile(string $file): ?string
    {
        $content = file_get_contents($file);

        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            $namespace = $matches[1];

            // Get class name from file
            if (preg_match('/(?:class|interface|trait)\s+(\w+)/', $content, $classMatches)) {
                return $namespace . '\\' . $classMatches[1];
            }
        }

        return null;
    }

    private function isAllowedException(string $fromLayer, string $toLayer, string $dependency): bool
    {
        if (! isset($this->exceptions[$fromLayer][$toLayer])) {
            return false;
        }

        return in_array($dependency, $this->exceptions[$fromLayer][$toLayer], true);
    }
}
