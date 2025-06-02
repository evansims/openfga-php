<?php

declare(strict_types=1);

namespace OpenFGA\Tools;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

// Load Composer autoloader
$autoloader = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloader)) {
    die("Error: Run 'composer install' in the tools/docs directory first.\n");
}

require_once $autoloader;

use Symfony\Component\Finder\Finder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DocumentationGenerator
{
    private string $srcDir;
    private string $outputDir;
    private Environment $twig;
    private array $classMap = [];

    public function __construct(string $srcDir, string $outputDir)
    {
        $this->srcDir = rtrim($srcDir, '/');
        $this->outputDir = rtrim($outputDir, '/');

        $loader = new FilesystemLoader(__DIR__);
        $this->twig = new Environment($loader);
    }

    public function generate(): void
    {
        $this->buildClassMap();
        $this->generateDocumentation();
    }

    private function buildClassMap(): void
    {
        $finder = new Finder();
        $finder->files()->in($this->srcDir)->name('*.php');
        $totalFiles = 0;
        $processedFiles = 0;

        echo "Scanning for PHP files in: " . $this->srcDir . "\n";

        foreach ($finder as $file) {
            $totalFiles++;
            $filePath = $file->getRealPath();
            $className = $this->getClassNameFromFile($filePath);

            if ($className) {
                $this->classMap[$className] = $filePath;
                $processedFiles++;

                // Only show progress for every 10 files to reduce noise
                if ($processedFiles % 10 === 0) {
                    echo "Processed $processedFiles files...\n";
                }
            } else {
                echo "Skipping file (no class/interface found): " . $file->getRelativePathname() . "\n";
            }
        }

        echo "Build complete. Processed $processedFiles of $totalFiles files. Found " . count($this->classMap) . " classes/interfaces.\n";
    }

    /**
     * Gets the current namespace from the backtrace
     */
    private function getCurrentNamespace(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        foreach ($backtrace as $frame) {
            if (isset($frame['class']) && str_starts_with($frame['class'], 'OpenFGA\\')) {
                $parts = explode('\\', $frame['class']);
                array_pop($parts); // Remove the class name
                return implode('\\', $parts);
            }
        }
        return 'OpenFGA';
    }

    private function getClassNameFromFile(string $file): ?string
    {
        static $cache = [];

        // Use cached result if available
        if (isset($cache[$file])) {
            return $cache[$file];
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            echo "[DEBUG] Could not read file: $file\n";
            $cache[$file] = null;
            return null;
        }

        // Skip files that don't contain a namespace
        if (!preg_match('/namespace\s+([^;]+);/s', $content, $namespaceMatches)) {
            echo "[DEBUG] No namespace found in file: $file\n";
            $cache[$file] = null;
            return null;
        }

        $namespace = $namespaceMatches[1];

        // Look for either class or interface definition at the beginning of a line
        // This regex ensures we match actual declarations, not text within docblocks
        // Supports all PHP class modifiers: abstract, final, readonly in any order
        if (preg_match('/^\s*(?:(?:abstract|final|readonly)\s+)*(class|interface)\s+(\w+)/m', $content, $matches)) {
            $type = $matches[1]; // 'class' or 'interface'
            $name = $matches[2];
            $className = $namespace . '\\' . $name;
            echo "[DEBUG] Found $type: $className in $file\n";
            $cache[$file] = $className;
            return $className;
        }

        echo "[DEBUG] No class or interface found in file: $file\n";
        $cache[$file] = null;
        return null;
    }

    private function generateDocumentation(): void
    {
        echo "Generating documentation...\n";
        $interfaceCount = 0;
        $classCount = 0;
        $skippedCount = 0;
        $totalInterfaces = 0;

        // First, count total interfaces for progress reporting
        foreach ($this->classMap as $className => $file) {
            try {
                $reflection = new ReflectionClass($className);
                if ($reflection->isInterface()) {
                    $totalInterfaces++;
                }
            } catch (\Exception $e) {
                // Ignore errors during counting
            }
        }

        // Now process all classes and interfaces
        $processedInterfaces = 0;

        foreach ($this->classMap as $className => $file) {
            try {
                $reflection = new ReflectionClass($className);
                $isInterface = $reflection->isInterface();

                // Skip abstract classes that are not interfaces
                if ($reflection->isAbstract() && !$isInterface) {
                    $skippedCount++;
                    continue;
                }

                if ($isInterface) {
                    $interfaceCount++;
                    $processedInterfaces++;
                    echo "Generating interface: $className ($processedInterfaces/$totalInterfaces)\n";
                } else {
                    $classCount++;
                    echo "Generating class: $className\n";
                }

                $this->generateClassDocumentation($className, $file, $isInterface);

            } catch (\Exception $e) {
                echo "Error processing $className: " . $e->getMessage() . "\n";
            }
        }

        echo "Documentation generation complete. Generated $classCount classes, $interfaceCount interfaces, and skipped $skippedCount abstract classes.\n";
    }

    private function generateClassDocumentation(string $className, string $file, bool $isInterface = false): void
    {
        $reflection = new ReflectionClass($className);

        // Skip abstract classes (but not interfaces)
        if ($reflection->isAbstract() && !$isInterface) {
            return;
        }

        // Calculate the output file path for relative link generation
        $namespacePath = str_replace('OpenFGA\\', '', $className);
        $namespacePath = str_replace('\\', '/', $namespacePath);
        $currentFilePath = $namespacePath . '.md';

        $classData = [
            'className' => $reflection->getShortName(),
            'namespace' => $reflection->getNamespaceName(),
            'isInterface' => $isInterface,
            'classDescription' => $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
            'sourceFile' => $this->getSourceFileLink($file),
            'interfaces' => array_map(function($interface) use ($reflection, $currentFilePath) {
                return $this->convertToMarkdownLink($interface->getName(), $reflection->getNamespaceName(), $currentFilePath);
            }, $reflection->getInterfaces()),
            'methods' => [],
            'constants' => [],
        ];

        // Process Constants if it's not an interface
        if (!$isInterface) {
            $reflectionConstants = $reflection->getReflectionConstants();
            foreach ($reflectionConstants as $constant) {
                if ($constant->isPublic()) {
                    $classData['constants'][] = [
                        'name' => $constant->getName(),
                        'value' => var_export($constant->getValue(), true),
                        'description' => $this->extractDescriptionFromDocComment($constant->getDocComment() ?: ''),
                    ];
                }
            }
        }

        // Get interface methods documentation if this is a class (not an interface)
        $interfaceMethods = [];
        if (!$isInterface) {
            $interfaceMethods = $this->getInterfaceMethodsDocumentation($reflection, $currentFilePath);
        }

        // Process methods
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isDestructor() || $method->isStatic()) { // Allow constructors
                continue;
            }

            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $methodData = [
                'name' => $method->getName(),
                'signature' => $this->getMethodSignature($method),
                'description' => $this->extractDescriptionFromDocComment($method->getDocComment() ?: ''),
                'sourceLink' => $this->getMethodSourceLink($method),
                'examples' => $this->extractExamplesFromDocComment($method->getDocComment() ?: ''),
                'category' => $this->categorizeMethod($method),
                'parameters' => [],
                'return' => [
                    'type' => $this->getReturnType($method, $reflection->getNamespaceName(), true, $currentFilePath),
                    'typeDisplay' => $this->escapeForTable($this->getReturnType($method, $reflection->getNamespaceName(), true, $currentFilePath)),
                    'description' => $this->extractReturnDescription($method->getDocComment() ?: ''),
                ],
            ];

            // Process parameters - validate against reflection
            $paramDescriptions = $this->validateAndExtractParameters($method);
            foreach ($method->getParameters() as $param) {
                $paramName = $param->getName();
                $methodData['parameters'][] = [
                    'name' => '$' . $paramName,
                    'type' => $this->getParameterType($param, $reflection->getNamespaceName(), true, $currentFilePath),
                    'typeDisplay' => $this->escapeForTable($this->getParameterType($param, $reflection->getNamespaceName(), true, $currentFilePath)),
                    'description' => $paramDescriptions[$paramName] ?? '',
                ];
            }

            // If this method is from an interface, merge the documentation
            if (isset($interfaceMethods[$method->getName()])) {
                $interfaceMethod = $interfaceMethods[$method->getName()];

                // Use interface method description if class method doesn't have one
                if (empty($methodData['description'])) {
                    $methodData['description'] = $interfaceMethod['description'];
                }

                // Merge parameter descriptions
                foreach ($methodData['parameters'] as &$param) {
                    $paramName = ltrim($param['name'], '$');
                    foreach ($interfaceMethod['parameters'] as $interfaceParam) {
                        if (ltrim($interfaceParam['name'], '$') === $paramName && !empty($interfaceParam['description'])) {
                            if (empty($param['description'])) {
                                $param['description'] = $interfaceParam['description'];
                            }
                            break;
                        }
                    }
                }

                // Use interface return description if class method doesn't have one
                if (empty($methodData['return']['description']) && !empty($interfaceMethod['return']['description'])) {
                    $methodData['return']['description'] = $interfaceMethod['return']['description'];
                }

                // Inherit examples from interface if class method doesn't have any
                if (empty($methodData['examples']) && !empty($interfaceMethod['examples'])) {
                    $methodData['examples'] = $interfaceMethod['examples'];
                }

                // Mark as from interface for rendering
                $methodData['fromInterface'] = $interfaceMethod['fromInterface'];

                // Remove from interface methods to avoid duplication
                unset($interfaceMethods[$method->getName()]);
            }

            $classData['methods'][] = $methodData;
        }

        // Generate output path
        $namespace = $reflection->getNamespaceName();

        // Handle root namespace (OpenFGA) differently
        if ($namespace === 'OpenFGA') {
            $outputPath = $this->outputDir;
        } else {
            $relativePath = str_replace('OpenFGA\\', '', $namespace);
            $relativePath = str_replace('\\', '/', $relativePath);
            $outputPath = $this->outputDir . '/' . $relativePath;

            if (!is_dir($outputPath)) {
                mkdir($outputPath, 0755, true);
            }
        }

        // Ensure the output directory exists
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        // Add any remaining interface methods that weren't implemented in the class
        foreach ($interfaceMethods as $methodData) {
            $methodData['isFromInterface'] = true;
            $classData['methods'][] = $methodData;
        }

        // Sort methods by category, then alphabetically
        usort($classData['methods'], function($a, $b) {
            $categoryOrder = [
                'Authorization' => 1,
                'CRUD Operations' => 2,
                'List Operations' => 3,
                'Store Management' => 4,
                'Model Management' => 5,
                'Tuple Operations' => 6,
                'Utility' => 7,
                'Other' => 8,
            ];
            
            $aCategoryWeight = $categoryOrder[$a['category']] ?? 999;
            $bCategoryWeight = $categoryOrder[$b['category']] ?? 999;
            
            if ($aCategoryWeight !== $bCategoryWeight) {
                return $aCategoryWeight <=> $bCategoryWeight;
            }
            
            return strcmp($a['name'], $b['name']);
        });
        
        // Add method statistics and related classes
        $classData['methodStats'] = $this->calculateMethodStatistics($classData['methods']);
        $classData['relatedClasses'] = $this->findRelatedClasses($reflection);

        // Render and save
        $outputFile = $outputPath . '/' . $reflection->getShortName() . '.md';
        echo "Writing to: $outputFile\n";
        $content = $this->twig->render('documentation.twig', $classData);

        $result = file_put_contents($outputFile, $content);
        if ($result === false) {
            echo "Failed to write to: $outputFile\n";
        }
    }

    /**
     * Extracts method documentation from all implemented interfaces
     *
     * @param ReflectionClass $reflection The reflection of the class to get interface methods from
     * @return array Array of method documentation keyed by method name
     */
    private function getInterfaceMethodsDocumentation(ReflectionClass $reflection, string $currentFilePath = ''): array
    {
        $interfaceMethods = [];

        // Get all interfaces recursively
        $interfaces = $reflection->getInterfaces();

        foreach ($interfaces as $interface) {
            foreach ($interface->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methodName = $method->getName();

                // Skip magic methods and constructors
                if (str_starts_with($methodName, '__')) {
                    continue;
                }

                // Only include if we haven't seen this method name before
                if (!isset($interfaceMethods[$methodName])) {
                    $methodData = [
                        'name' => $methodName,
                        'signature' => $this->getMethodSignature($method),
                        'description' => $this->extractDescriptionFromDocComment($method->getDocComment() ?: ''),
                        'sourceLink' => $this->getMethodSourceLink($method),
                        'examples' => $this->extractExamplesFromDocComment($method->getDocComment() ?: ''),
                        'category' => $this->categorizeMethod($method),
                        'parameters' => [],
                        'return' => [
                            'type' => $this->getReturnType($method, $interface->getNamespaceName(), true, $currentFilePath),
                            'typeDisplay' => $this->escapeForTable($this->getReturnType($method, $interface->getNamespaceName(), true, $currentFilePath)),
                            'description' => $this->extractReturnDescription($method->getDocComment() ?: ''),
                        ],
                        'fromInterface' => $interface->getName(),
                    ];

                    // Process parameters - validate against reflection
                    $paramDescriptions = $this->validateAndExtractParameters($method);
                    foreach ($method->getParameters() as $param) {
                        $paramName = $param->getName();
                        $methodData['parameters'][] = [
                            'name' => '$' . $paramName,
                            'type' => $this->getParameterType($param, $interface->getNamespaceName(), true, $currentFilePath),
                            'typeDisplay' => $this->escapeForTable($this->getParameterType($param, $interface->getNamespaceName(), true, $currentFilePath)),
                            'description' => $paramDescriptions[$paramName] ?? '',
                        ];
                    }

                    $interfaceMethods[$methodName] = $methodData;
                }
            }
        }

        return $interfaceMethods;
    }

    private function getMethodSignature(ReflectionMethod $method): string
    {
        $params = [];
        $namespace = $method->getDeclaringClass()->getNamespaceName();
        
        foreach ($method->getParameters() as $param) {
            // Get raw type without markdown links for the signature
            $paramStr = $this->getParameterType($param, $namespace, false) . ' ';
            $paramStr .= ($param->isPassedByReference() ? '&' : '') . '$' . $param->getName();

            if ($param->isDefaultValueAvailable()) {
                $default = $param->getDefaultValue();
                $defaultValueStr = '';

                if (is_object($default)) {
                    $defaultClassRef = new ReflectionClass(get_class($default));
                    if ($defaultClassRef->isEnum()) {
                        // Don't use markdown links in method signatures
                        $defaultValueStr = $defaultClassRef->getName() . '::' . $default->name;
                    } else {
                        // Non-enum objects - default to var_export
                        $defaultValueStr = var_export($default, true);
                    }
                } elseif (is_array($default)) {
                    $exportedParts = [];
                    $isList = (array_keys($default) === range(0, count($default) - 1)); // Simple list check

                    foreach ($default as $key => $value) {
                        $exportedValue = '';
                        if (is_object($value)) {
                            $valueClassRef = new ReflectionClass(get_class($value));
                            if ($valueClassRef->isEnum()) {
                                // Don't use markdown links in method signatures
                                $exportedValue = $valueClassRef->getName() . '::' . $value->name;
                            } else {
                                $exportedValue = var_export($value, true);
                            }
                        } else {
                            $exportedValue = var_export($value, true);
                        }

                        if ($isList) {
                            $exportedParts[] = $exportedValue;
                        } else {
                            $exportedKey = var_export($key, true);
                            $exportedParts[] = $exportedKey . ' => ' . $exportedValue;
                        }
                    }
                    if (empty($exportedParts)) {
                        $defaultValueStr = '[]';
                    } elseif ($isList) {
                        $defaultValueStr = '[' . implode(', ', $exportedParts) . ']';
                    } else {
                        $defaultValueStr = '[' . implode(', ', $exportedParts) . ']'; // For associative arrays
                    }
                } else {
                    // Scalars, null
                    $defaultValueStr = var_export($default, true);
                }
                $paramStr .= ' = ' . $defaultValueStr;
            }

            $params[] = $paramStr;
        }

        // Get raw return type without markdown links for the signature
        $returnType = $this->getReturnType($method, $namespace, false);
        $returnTypeStr = $returnType ? ': ' . $returnType : '';

        // For multi-line formatting, check if the signature would be long
        $singleLineSignature = sprintf(
            'public function %s(%s)%s',
            $method->getName(),
            implode(', ', $params),
            $returnTypeStr
        );
        
        // If signature is longer than 120 characters or has more than 3 parameters, use multi-line format
        if (strlen($singleLineSignature) > 120 || count($params) > 3) {
            if (empty($params)) {
                return sprintf('public function %s()%s', $method->getName(), $returnTypeStr);
            }
            
            return sprintf(
                "public function %s(\n    %s,\n)%s",
                $method->getName(),
                implode(",\n    ", $params),
                $returnTypeStr
            );
        }
        
        return $singleLineSignature;
    }

    private function getParameterType(ReflectionParameter $param, string $namespace = 'OpenFGA', bool $withLinks = true, string $currentFilePath = ''): string
    {
        // Try to get the type from PHPDoc first
        $method = $param->getDeclaringFunction();
        if ($method instanceof ReflectionMethod) {
            $docComment = $method->getDocComment();
            if ($docComment) {
                $paramType = $this->extractParamTypeFromDocComment($docComment, $param->getName());
                if ($paramType) {
                    return $withLinks ? $this->convertToMarkdownLink($paramType, $namespace, $currentFilePath) : $paramType;
                }
            }
        }

        // Fall back to reflection-based type
        if ($param->hasType()) {
            $type = $param->getType();
            $typeStr = (string) $type;

            // Handle nullable types - check if already nullable from string representation
            if ($type->allowsNull() && $typeStr !== 'mixed' && !str_starts_with($typeStr, '?')) {
                $typeStr = '?' . $typeStr;
            }

            return $withLinks ? $this->convertToMarkdownLink($typeStr, $namespace, $currentFilePath) : $typeStr;
        }

        return 'mixed';
    }

    private function getReturnType(ReflectionMethod $method, string $namespace = 'OpenFGA', bool $withLinks = true, string $currentFilePath = ''): string
    {
        // First, try to get the return type from PHPDoc
        $docComment = $method->getDocComment();
        if ($docComment) {
            $returnType = $this->extractReturnTypeFromDocComment($docComment);
            if ($returnType) {
                return $withLinks ? $this->convertToMarkdownLink($returnType, $namespace, $currentFilePath) : $returnType;
            }
        }

        // Fall back to reflection-based return type
        if ($method->hasReturnType()) {
            $returnType = $method->getReturnType();
            $typeStr = (string) $returnType;

            // Handle nullable return types - check if already nullable from string representation
            if ($returnType->allowsNull() && $typeStr !== 'mixed' && !str_starts_with($typeStr, '?')) {
                $typeStr = '?' . $typeStr;
            }

            return $withLinks ? $this->convertToMarkdownLink($typeStr, $namespace, $currentFilePath) : $typeStr;
        }

        return '';
    }

    private function convertToMarkdownLink(string $type, string $currentNamespace = 'OpenFGA', string $currentFilePath = ''): string
    {
        // Handle union types
        if (str_contains($type, '|')) {
            $types = array_map('trim', explode('|', $type));
            $convertedTypes = array_map(fn($t) => $this->convertToMarkdownLink($t, $currentNamespace, $currentFilePath), $types);
            return implode(' | ', $convertedTypes); // Added spaces around |
        }

        // Handle array types (e.g., string[] or Type[])
        $isArray = false;
        if (str_ends_with($type, '[]')) {
            $isArray = true;
            $type = substr($type, 0, -2);
        }

        // Handle nullable types
        $isNullable = str_starts_with($type, '?');
        $type = ltrim($type, '?');

        // Handle generic types (e.g., array<string, Type>)
        $genericSuffix = '';
        if (preg_match('/([^<]*)<(.+)>/', $type, $matches)) {
            $type = $matches[1];
            $genericParams = array_map('trim', explode(',', $matches[2]));
            $convertedParams = array_map(fn($p) => $this->convertToMarkdownLink($p, $currentNamespace, $currentFilePath), $genericParams);
            $genericSuffix = '<' . implode(', ', $convertedParams) . '>';
        }

        // Expanded list of built-in types
        $builtInTypes = [
            // Basic types
            'string', 'int', 'integer', 'bool', 'boolean', 'float', 'double',
            'array', 'object', 'mixed', 'null', 'true', 'false', 'void', 'iterable',
            'callable', 'self', 'static', 'parent', 'resource', 'scalar', 'number',
            'callback', 'never', 'class-string', 'array-key', 'int|string',
            // PHP 8.0+ types
            'positive-int', 'negative-int', 'non-empty-array', 'non-empty-string', 'numeric',
            'numeric-string', 'trait-string', 'array-key', 'list', 'callable-string',
            // PHP 8.1+ types
            'int-mask', 'int-mask-of', 'non-empty-list', 'non-empty-array', 'non-empty-string',
            'non-falsy-string', 'non-empty-lowercase-string', 'non-empty-uppercase-string',
            'non-empty-non-whitespace-string', 'non-empty-numeric-string', 'non-empty-scalar',
            // Common type aliases
            'boolean', 'integer', 'double', 'real', 'number', 'numeric', 'scalar',
            // PSR and framework specific
            '\DateTimeInterface', '\DateTime', '\DateTimeImmutable', '\Traversable',
            '\ArrayAccess', '\Countable', '\Iterator', '\IteratorAggregate', '\Serializable',
            '\Stringable', '\Throwable', '\Closure', '\Generator', '\WeakReference',
            // Common interface/class names that might appear in docblocks
            'stdClass', 'ArrayObject', 'SplFileInfo', 'SplFileObject', 'SplTempFileObject',
            'SplDoublyLinkedList', 'SplFixedArray', 'SplHeap', 'SplMaxHeap', 'SplMinHeap',
            'SplObjectStorage', 'SplPriorityQueue', 'SplQueue', 'SplStack', 'SplDoublyLinkedList',
            // PSR interfaces
            'Psr\Http\Message\MessageInterface', 'Psr\Http\Message\RequestInterface',
            'Psr\Http\Message\ResponseInterface', 'Psr\Http\Message\ServerRequestInterface',
            'Psr\Http\Message\StreamInterface', 'Psr\Http\Message\UploadedFileInterface',
            'Psr\Http\Message\UriInterface', 'Psr\Log\LoggerInterface',
            // Common scalar types with different casing
            'String', 'Int', 'Integer', 'Bool', 'Boolean', 'Float', 'Double', 'Array', 'Object',
            'Mixed', 'Null', 'True', 'False', 'Void', 'Iterable', 'Callable', 'Self', 'Static',
            'Parent', 'Resource', 'Scalar', 'Number', 'Callback', 'Never', 'Class-string',
            'Array-key', 'Positive-int', 'Negative-int', 'Non-empty-array', 'Non-empty-string',
            'Numeric', 'Numeric-string', 'Trait-string', 'List', 'Callable-string', 'Int-mask',
            'Int-mask-of', 'Non-empty-list', 'Non-empty-array', 'Non-empty-string', 'Non-falsy-string',
            'Non-empty-lowercase-string', 'Non-empty-uppercase-string', 'Non-empty-non-whitespace-string',
            'Non-empty-numeric-string', 'Non-empty-scalar'
        ];

        $lowerType = strtolower($type);
        if (in_array($lowerType, array_map('strtolower', $builtInTypes), true)) {
            $result = $type . $genericSuffix;
            if ($isArray) $result .= '[]';
            return ($isNullable ? '?' : '') . $result;
        }

        // Check if it's a class in our SDK
        $isInternalClass = false;
        $relativePath = '';
        $fullTypeName = $type;
        $displayName = $type;

        // Handle fully qualified class names
        if (str_starts_with($type, 'OpenFGA\\')) {
            $isInternalClass = array_key_exists($type, $this->classMap);
            $relativePath = str_replace('OpenFGA\\', '', $type);
            $relativePath = str_replace('\\', '/', $relativePath);
            $displayName = substr($type, strrpos($type, '\\') + 1);
        }
        // Handle relative class names (already in our SDK)
        elseif (array_key_exists('OpenFGA\\' . $type, $this->classMap)) {
            $isInternalClass = true;
            $fullTypeName = 'OpenFGA\\' . $type;
            $relativePath = str_replace('\\', '/', $type);
            $displayName = $type;
        }
        // Handle relative class names with namespace parts
        else {
            // Try to find the class in the provided namespace
            $possibleFullName = $currentNamespace . '\\' . $type;
            if (array_key_exists($possibleFullName, $this->classMap)) {
                $isInternalClass = true;
                $fullTypeName = $possibleFullName;
                $relativePath = str_replace('OpenFGA\\', '', $possibleFullName);
                $relativePath = str_replace('\\', '/', $relativePath);
                $displayName = $type;
            } else {
                // If not found in current namespace, try common OpenFGA sub-namespaces
                $commonNamespaces = [
                    'OpenFGA\\Models',
                    'OpenFGA\\Models\\Collections',
                    'OpenFGA\\Results',
                    'OpenFGA\\Requests',
                    'OpenFGA\\Responses',
                    'OpenFGA\\Authentication',
                    'OpenFGA\\Exceptions',
                    'OpenFGA\\Models\\Enums',
                ];
                
                foreach ($commonNamespaces as $namespace) {
                    $possibleFullName = $namespace . '\\' . $type;
                    if (array_key_exists($possibleFullName, $this->classMap)) {
                        $isInternalClass = true;
                        $fullTypeName = $possibleFullName;
                        $relativePath = str_replace('OpenFGA\\', '', $possibleFullName);
                        $relativePath = str_replace('\\', '/', $relativePath);
                        $displayName = $type;
                        break;
                    }
                }
            }
        }

        // If it's an internal class, create a markdown link
        if ($isInternalClass) {
            // Calculate relative path from current file to target file
            $targetPath = $relativePath . '.md';
            if (!empty($currentFilePath)) {
                $currentDir = dirname($currentFilePath);
                $targetDir = dirname($relativePath);
                
                // If both files are in the same directory, use just the filename
                if ($currentDir === $targetDir) {
                    $targetPath = basename($relativePath) . '.md';
                } elseif ($currentDir !== '.') {
                    // Calculate relative path between different directories
                    $currentDepth = substr_count($currentDir, '/');
                    $targetPath = str_repeat('../', $currentDepth) . $targetPath;
                }
            }
            $result = "[$displayName]($targetPath)";
        } else {
            // For external types, just use the short name
            $result = $type;
            if (str_contains($type, '\\')) {
                $result = substr($type, strrpos($type, '\\') + 1);
            }
        }

        // Add back generic suffix, array brackets, and nullable
        $result .= $genericSuffix;
        if ($isArray) $result .= '[]';
        return ($isNullable ? '?' : '') . $result;
    }

    private function extractDescriptionFromDocComment(string $docComment): string
    {
        if (empty($docComment)) {
            return '';
        }

        $lines = explode("\n", $docComment);
        $description = [];
        $inDescription = true;

        foreach ($lines as $line) {
            $line = trim($line, "/* \t");

            // Skip empty lines and the opening /**
            if (empty($line) || $line === '/**') {
                continue;
            }

            // Stop processing if we hit a tag and we're in the main description
            if ($inDescription && str_starts_with($line, '@')) {
                $inDescription = false;
                continue;
            }

            // Only process the main description
            if ($inDescription) {
                // If the line *is* @inheritDoc (case-insensitive), don't add it to the description text.
                if (strtolower(trim($line)) === '@inheritdoc') {
                    continue;
                }
                $description[] = $line;
            }
        }

        $result = trim(implode(" ", $description));

        // Clean up any remaining asterisks or slashes from the joined string
        $result = trim($result, "*/ \t");
        // Final check in case the description somehow ended up being only "@inheritdoc"
        if (strtolower($result) === '@inheritdoc') {
            return '';
        }

        return $result;
    }

    private function extractParamDescription(string $docComment, string $paramName): string
    {
        if (empty($docComment) || empty($paramName)) {
            return '';
        }

        $lines = explode("\n", $docComment);
        $descriptionLines = [];
        $capturing = false;
        // Regex to find the specific @param line for $paramName.
        // It ensures that $paramName is a whole word to avoid partial matches.
        $paramRegex = '/@param\s+[\w\\\\\[\]]+(?:<[^>]+>)?(?:\s*\|\s*[\w\\\\\[\]]+(?:<[^>]+>)?)*\s+\$' . preg_quote($paramName, '/') . '(?:\s+(.*))?$/';

        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");

            if ($capturing) {
                // If the line starts with a new PHPDoc tag, stop capturing.
                if (preg_match('/^@\w+/', $trimmedLine)) {
                    $capturing = false;
                    break;
                }
                // Add non-empty lines to the description.
                if (!empty($trimmedLine)) {
                    $descriptionLines[] = $trimmedLine;
                }
            } else {
                if (preg_match($paramRegex, $trimmedLine, $matches)) {
                    $capturing = true;
                    if (!empty($matches[1])) { // Description starts on the same line.
                        $descriptionLines[] = trim($matches[1]);
                    }
                }
            }
        }
        return trim(implode(' ', $descriptionLines));
    }

    private function extractReturnDescription(string $docComment): string
    {
        if (empty($docComment)) {
            return '';
        }

        $lines = explode("\n", $docComment);
        $descriptionLines = [];
        $capturing = false;
        $returnRegex = '/@return\s+[\w\\\\\[\]]+(?:<[^>]+>)?(?:\s*\|\s*[\w\\\\\[\]]+(?:<[^>]+>)?)*(?:\s+(.*))?$/';

        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");

            if ($capturing) {
                // If the line starts with a new PHPDoc tag, stop capturing.
                if (preg_match('/^@\w+/', $trimmedLine)) {
                    $capturing = false;
                    break;
                }
                // Add non-empty lines to the description.
                 if (!empty($trimmedLine)) {
                    $descriptionLines[] = $trimmedLine;
                }
            } else {
                if (preg_match($returnRegex, $trimmedLine, $matches)) {
                    $capturing = true;
                    if (!empty($matches[1])) { // Description starts on the same line.
                        $descriptionLines[] = trim($matches[1]);
                    }
                }
            }
        }
        return trim(implode("\n", $descriptionLines));
    }

    private function extractReturnTypeFromDocComment(string $docComment): ?string
    {
        if (empty($docComment)) {
            return null;
        }

        $lines = explode("\n", $docComment);
        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");
            
            // Match @return type - capture the full type including generics
            // This regex matches: type or type<...> or type1|type2 etc
            if (preg_match('/@return\s+([\w\\\\\[\]]+(?:<[^>]+>)?(?:\s*\|\s*[\w\\\\\[\]]+(?:<[^>]+>)?)*)(?:\s+.*)?$/', $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }

    private function extractParamTypeFromDocComment(string $docComment, string $paramName): ?string
    {
        if (empty($docComment) || empty($paramName)) {
            return null;
        }

        $lines = explode("\n", $docComment);
        // Create regex that matches @param type $paramName
        // Support complex types like array<string, mixed>
        $paramRegex = '/@param\s+([\w\\\\\[\]]+(?:<[^>]+>)?(?:\s*\|\s*[\w\\\\\[\]]+(?:<[^>]+>)?)*)\s+\$' . preg_quote($paramName, '/') . '\b/';
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");
            
            if (preg_match($paramRegex, $trimmedLine, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return null;
    }

    /**
     * Escape angle brackets for display in markdown tables
     */
    private function escapeForTable(string $type): string
    {
        // Replace characters that can break markdown table formatting
        return str_replace(['<', '>', '|'], ['&lt;', '&gt;', '&#124;'], $type);
    }

    /**
     * Generate a GitHub source link for a file
     */
    private function getSourceFileLink(string $filePath): string
    {
        // Convert absolute path to relative path from project root
        $relativePath = $this->getRelativePathFromProjectRoot($filePath);
        return "https://github.com/evansims/openfga-php/blob/main/{$relativePath}";
    }

    /**
     * Generate a GitHub source link for a method with line number
     */
    private function getMethodSourceLink(ReflectionMethod $method): string
    {
        $filePath = $method->getFileName();
        if (!$filePath) {
            return '';
        }

        $relativePath = $this->getRelativePathFromProjectRoot($filePath);
        $lineNumber = $method->getStartLine();
        
        return "https://github.com/evansims/openfga-php/blob/main/{$relativePath}#L{$lineNumber}";
    }

    /**
     * Convert absolute file path to relative path from project root
     */
    private function getRelativePathFromProjectRoot(string $filePath): string
    {
        // Find the project root by looking for composer.json
        $projectRoot = $this->findProjectRoot($filePath);
        
        if ($projectRoot) {
            return str_replace($projectRoot . '/', '', $filePath);
        }
        
        // Fallback: assume src directory structure
        if (str_contains($filePath, '/src/')) {
            $parts = explode('/src/', $filePath);
            return 'src/' . end($parts);
        }
        
        return basename($filePath);
    }

    /**
     * Find the project root directory by looking for composer.json
     */
    private function findProjectRoot(string $startPath): ?string
    {
        $currentPath = is_file($startPath) ? dirname($startPath) : $startPath;
        
        while ($currentPath !== '/' && $currentPath !== '') {
            if (file_exists($currentPath . '/composer.json')) {
                return $currentPath;
            }
            $currentPath = dirname($currentPath);
        }
        
        return null;
    }

    /**
     * Validate PHPDoc parameters match method signature and extract descriptions
     */
    private function validateAndExtractParameters(ReflectionMethod $method): array
    {
        $docComment = $method->getDocComment();
        if (!$docComment) {
            return [];
        }

        // Get actual parameter names from reflection
        $reflectionParams = array_map(fn($p) => $p->getName(), $method->getParameters());
        
        // Extract parameter descriptions from PHPDoc
        $paramDescriptions = [];
        $lines = explode("\n", $docComment);
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");
            
            // Match @param with flexible type and parameter name
            if (preg_match('/@param\s+[^\s]+\s+\$([a-zA-Z_][a-zA-Z0-9_]*)(?:\s+(.*))?$/', $trimmedLine, $matches)) {
                $paramName = $matches[1];
                $description = isset($matches[2]) ? trim($matches[2]) : '';
                
                // Only include if parameter actually exists in method signature
                if (in_array($paramName, $reflectionParams, true)) {
                    $paramDescriptions[$paramName] = $description;
                }
            }
        }
        
        return $paramDescriptions;
    }

    /**
     * Extract code examples from @example tags in docblocks
     */
    private function extractExamplesFromDocComment(string $docComment): array
    {
        if (empty($docComment)) {
            return [];
        }

        $examples = [];
        $lines = explode("\n", $docComment);
        $currentExample = null;
        $inExample = false;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line, "/* \t\n\r");
            
            if (preg_match('/@example(?:\s+(.*))?$/', $trimmedLine, $matches)) {
                // Start of new example
                if ($currentExample !== null) {
                    $examples[] = $currentExample;
                }
                $currentExample = [
                    'title' => isset($matches[1]) ? trim($matches[1]) : 'Example',
                    'code' => []
                ];
                $inExample = true;
            } elseif ($inExample) {
                // Check if we hit another tag
                if (preg_match('/^@\w+/', $trimmedLine)) {
                    $inExample = false;
                    if ($currentExample !== null) {
                        $examples[] = $currentExample;
                        $currentExample = null;
                    }
                } else {
                    // Add line to current example, preserving original indentation from docblock
                    if ($currentExample !== null) {
                        // Remove only the comment prefix (* and whitespace), preserve code indentation
                        $codeLine = preg_replace('/^\s*\*\s?/', '', $line);
                        $currentExample['code'][] = $codeLine;
                    }
                }
            }
        }
        
        // Add final example if exists
        if ($currentExample !== null) {
            $examples[] = $currentExample;
        }
        
        // Clean up examples
        foreach ($examples as &$example) {
            // Join lines and clean up formatting
            $codeLines = $example['code'];
            
            // Remove empty lines at start and end
            while (!empty($codeLines) && trim($codeLines[0]) === '') {
                array_shift($codeLines);
            }
            while (!empty($codeLines) && trim(end($codeLines)) === '') {
                array_pop($codeLines);
            }
            
            // Find minimum indentation (excluding empty lines)
            $minIndent = PHP_INT_MAX;
            foreach ($codeLines as $line) {
                if (trim($line) !== '') {
                    $indent = strlen($line) - strlen(ltrim($line));
                    $minIndent = min($minIndent, $indent);
                }
            }
            
            // Remove common indentation and normalize
            if ($minIndent > 0 && $minIndent !== PHP_INT_MAX) {
                $codeLines = array_map(function($line) use ($minIndent) {
                    if (trim($line) === '') {
                        return '';
                    }
                    return substr($line, $minIndent);
                }, $codeLines);
            }
            
            $example['code'] = implode("\n", $codeLines);
        }
        
        return $examples;
    }

    /**
     * Categorize method by common patterns
     */
    private function categorizeMethod(ReflectionMethod $method): string
    {
        $methodName = strtolower($method->getName());
        
        $categories = [
            'Authorization' => ['check', 'expand', 'allow', 'verify', 'validate'],
            'CRUD Operations' => ['create', 'read', 'write', 'delete', 'update', 'remove'],
            'List Operations' => ['list', 'get', 'find', 'search', 'fetch'],
            'Store Management' => ['store', 'createstore', 'deletestore', 'getstore'],
            'Model Management' => ['model', 'authorization', 'schema'],
            'Tuple Operations' => ['tuple', 'relation', 'relationship'],
            'Utility' => ['assert', 'get', 'set', 'has', 'is'],
        ];
        
        foreach ($categories as $category => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($methodName, $pattern)) {
                    return $category;
                }
            }
        }
        
        return 'Other';
    }
    
    /**
     * Calculate method statistics for overview
     */
    private function calculateMethodStatistics(array $methods): array
    {
        $stats = [
            'total' => count($methods),
            'categories' => [],
            'withExamples' => 0,
            'withDescription' => 0,
        ];
        
        foreach ($methods as $method) {
            $category = $method['category'];
            $stats['categories'][$category] = ($stats['categories'][$category] ?? 0) + 1;
            
            if (!empty($method['examples'])) {
                $stats['withExamples']++;
            }
            
            if (!empty($method['description'])) {
                $stats['withDescription']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Find classes related to the current class
     */
    private function findRelatedClasses(ReflectionClass $class): array
    {
        $related = [];
        $className = $class->getName();
        $shortName = $class->getShortName();
        
        foreach ($this->classMap as $otherClassName => $filePath) {
            if ($otherClassName === $className) {
                continue;
            }
            
            $otherShortName = substr($otherClassName, strrpos($otherClassName, '\\') + 1);
            
            // Look for naming patterns that suggest relationships
            $patterns = [
                // Interface/Implementation pairs
                $shortName . 'Interface' => 'interface',
                str_replace('Interface', '', $shortName) => 'implementation',
                // Request/Response pairs
                str_replace('Request', 'Response', $shortName) => 'response',
                str_replace('Response', 'Request', $shortName) => 'request',
                // Collection relationships
                str_replace('s', '', $shortName) . 's' => 'collection',
                str_replace('Collection', '', $shortName) => 'item',
            ];
            
            foreach ($patterns as $pattern => $relationship) {
                if ($otherShortName === $pattern) {
                    $related[] = [
                        'class' => $otherClassName,
                        'shortName' => $otherShortName,
                        'relationship' => $relationship,
                        'link' => $this->generateRelatedClassLink($otherClassName, $class->getNamespaceName())
                    ];
                }
            }
        }
        
        return $related;
    }
    
    /**
     * Generate markdown link for related class
     */
    private function generateRelatedClassLink(string $targetClassName, string $currentNamespace): string
    {
        $targetPath = str_replace('OpenFGA\\', '', $targetClassName);
        $targetPath = str_replace('\\', '/', $targetPath);
        $currentPath = str_replace('OpenFGA\\', '', $currentNamespace);
        $currentPath = str_replace('\\', '/', $currentPath);
        
        $targetShortName = substr($targetClassName, strrpos($targetClassName, '\\') + 1);
        
        // Calculate relative path
        if ($currentPath !== '') {
            $currentDepth = substr_count($currentPath, '/');
            $relativePath = str_repeat('../', $currentDepth) . $targetPath . '.md';
        } else {
            $relativePath = $targetPath . '.md';
        }
        
        return "[$targetShortName]($relativePath)";
    }

    public static function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                self::deleteDir($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}

if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $options = getopt('', ['src::', 'out::', 'clean']);

    $srcDir = $options['src'] ?? __DIR__ . '/../../src';
    $outputDir = $options['out'] ?? __DIR__ . '/../../docs/API';

    if (isset($options['clean']) && is_dir($outputDir)) {
        DocumentationGenerator::deleteDir($outputDir);
    }

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    $generator = new DocumentationGenerator($srcDir, $outputDir);
    $generator->generate();

    echo "Documentation generated successfully!\n";
}
