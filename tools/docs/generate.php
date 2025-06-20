<?php

declare(strict_types=1);

namespace OpenFGA\Tools;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use OpenFGA\Language;
use OpenFGA\Translation\YamlParser;

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
    private const TRANSLATION_FILE_REGEX = '/messages\.([a-z]{2,}(?:_[A-Z]{2,})?)\.yaml$/';

    private string $srcDir;
    private string $outputDir;
    private Environment $twig;
    private array $classMap = [];
    private string $projectRoot;

    public function __construct(string $srcDir, string $outputDir)
    {
        $this->srcDir = rtrim($srcDir, '/');
        $this->outputDir = rtrim($outputDir, '/');
        $this->projectRoot = $this->findProjectRootFromSrc($srcDir);

        $loader = new FilesystemLoader(__DIR__);
        $this->twig = new Environment($loader);
    }

    public function generate(): void
    {
        $this->buildClassMap();
        $this->generateDocumentation();
        $this->generateTableOfContents();
        $this->generateMainApiIndex();
    }

    private function buildClassMap(): void
    {
        $finder = new Finder();
        $finder->files()->in($this->srcDir)->name('*.php');
        $totalFiles = 0;
        $processedFiles = 0;

        // echo "Scanning for PHP files in: " . $this->srcDir . "\n";

        foreach ($finder as $file) {
            $totalFiles++;
            $filePath = $file->getRealPath();
            $className = $this->getClassNameFromFile($filePath);

            if ($className) {
                $this->classMap[$className] = $filePath;
                $processedFiles++;

                // Only show progress for every 10 files to reduce noise
                if ($processedFiles % 10 === 0) {
                    // echo "Processed $processedFiles files...\n";
                }
            } else {
                // echo "Skipping file (no class/interface found): " . $file->getRelativePathname() . "\n";
            }
        }

        // echo "Build complete. Processed $processedFiles of $totalFiles files. Found " . count($this->classMap) . " classes/interfaces.\n";
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
            // echo "[DEBUG] Could not read file: $file\n";
            $cache[$file] = null;
            return null;
        }

        // Skip files that don't contain a namespace
        if (!preg_match('/namespace\s+([^;]+);/s', $content, $namespaceMatches)) {
            // echo "[DEBUG] No namespace found in file: $file\n";
            $cache[$file] = null;
            return null;
        }

        $namespace = $namespaceMatches[1];

        // Look for class, interface, or enum definition at the beginning of a line
        // This regex ensures we match actual declarations, not text within docblocks
        // Supports all PHP class modifiers: abstract, final, readonly in any order
        if (preg_match('/^\s*(?:(?:abstract|final|readonly)\s+)*(class|interface|enum)\s+(\w+)/m', $content, $matches)) {
            $type = $matches[1]; // 'class', 'interface', or 'enum'
            $name = $matches[2];
            $className = $namespace . '\\' . $name;
            // echo "[DEBUG] Found $type: $className in $file\n";
            $cache[$file] = $className;
            return $className;
        }

        // echo "[DEBUG] No class, interface, or enum found in file: $file\n";
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
                $isEnum = $reflection->isEnum();

                // Skip abstract classes that are not interfaces or enums
                if ($reflection->isAbstract() && !$isInterface && !$isEnum) {
                    $skippedCount++;
                    continue;
                }

                if ($isInterface) {
                    $interfaceCount++;
                    $processedInterfaces++;
                    // echo "Generating interface: $className ($processedInterfaces/$totalInterfaces)\n";
                } elseif ($isEnum) {
                    $classCount++;
                    // echo "Generating enum: $className\n";
                } else {
                    $classCount++;
                    // echo "Generating class: $className\n";
                }

                $this->generateClassDocumentation($className, $file, $isInterface, $isEnum);

            } catch (\Exception $e) {
                echo "Error processing $className: " . $e->getMessage() . "\n";
            }
        }

        echo "Generated $classCount classes, $interfaceCount interfaces, and skipped $skippedCount abstract classes.\n";
    }

    protected function generateClassDocumentation(string $className, string $file, bool $isInterface = false, bool $isEnum = false): void
    {
        $reflection = new ReflectionClass($className);

        // Skip abstract classes (but not interfaces or enums)
        if ($reflection->isAbstract() && !$isInterface && !$isEnum) {
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
            'isEnum' => $isEnum,
            'classDescription' => $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
            'sourceFile' => $this->getSourceFileLink($file),
            'interfaces' => array_map(function($interface) use ($reflection, $currentFilePath) {
                return $this->convertToMarkdownLink($interface->getName(), $reflection->getNamespaceName(), $currentFilePath);
            }, $reflection->getInterfaces()),
            'methods' => [],
            'constants' => [],
            'cases' => [],
        ];

        // Process Constants if it's not an interface
        if (!$isInterface) {
            $reflectionConstants = $reflection->getReflectionConstants();
            foreach ($reflectionConstants as $constant) {
                if ($constant->isPublic()) {
                    $classData['constants'][] = [
                        'name' => $constant->getName(),
                        'value' => $this->formatConstantValue($constant->getValue()),
                        'description' => $this->extractDescriptionFromDocComment($constant->getDocComment() ?: ''),
                    ];
                }
            }
        }

        // Process Enum Cases if it's an enum
        if ($isEnum) {
            $reflectionEnum = new \ReflectionEnum($className);
            $isBacked = $reflectionEnum->isBacked();
            foreach ($reflectionEnum->getCases() as $case) {
                $value = null;
                if ($isBacked && $case instanceof \ReflectionEnumBackedCase) {
                    $value = $case->getBackingValue();
                }
                $classData['cases'][] = [
                    'name' => $case->getName(),
                    'value' => $value,
                    'description' => $this->extractDescriptionFromDocComment($case->getDocComment() ?: ''),
                ];
            }
        }

        // Get interface methods documentation if this is a class (not an interface or enum)
        $interfaceMethods = [];
        if (!$isInterface && !$isEnum) {
            $interfaceMethods = $this->getInterfaceMethodsDocumentation($reflection, $currentFilePath);
        }

        // Process methods (skip for enums unless they have custom methods)
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
                unset($param); // Destroy the reference to avoid issues

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

        // Sort methods alphabetically by name
        usort($classData['methods'], function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Add method statistics and related classes
        $classData['methodStats'] = $this->calculateMethodStatistics($classData['methods']);
        $classData['relatedClasses'] = $this->findRelatedClasses($reflection);

        // Add translation tables for Messages class
        if ($className === 'OpenFGA\Messages') {
            $translationData = $this->loadTranslationData();
            $classData['translations'] = $this->organizeMessageTranslations($translationData);
            $classData['availableLocales'] = array_keys($translationData);
        }

        // Render and save
        $outputFile = $outputPath . '/' . $reflection->getShortName() . '.md';
        // echo "Writing to: $outputFile\n";
        $content = $this->twig->render('documentation.twig', $classData);

        // Clean up markdown formatting
        $content = $this->cleanupMarkdown($content);

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

        // Handle array types (for example string[] or Type[])
        $isArray = false;
        if (str_ends_with($type, '[]')) {
            $isArray = true;
            $type = substr($type, 0, -2);
        }

        // Handle nullable types
        $isNullable = str_starts_with($type, '?');
        $type = ltrim($type, '?');

        // Handle generic types (for example array<string, Type>)
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
            $result = '`' . $type . $genericSuffix . '`';
            if ($isArray) $result .= '[]';
            if ($isNullable) $result .= ' | `null`';
            return $result;
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
            $result = "[`$displayName`]($targetPath)";
        } else {
            // For external types, wrap in backticks and use the short name
            $result = $type;
            if (str_contains($type, '\\')) {
                $result = substr($type, strrpos($type, '\\') + 1);
            }
            $result = '`' . $result . '`';
        }

        // Add back generic suffix, array brackets, and nullable
        $result .= $genericSuffix;
        if ($isArray) $result .= '[]';
        if ($isNullable) $result .= ' | `null`';
        return $result;
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

    /**
     * Clean up markdown formatting for better readability and consistency.
     *
     * This method applies markdown best practices:
     * - Removes excessive blank lines (max 2 consecutive)
     * - Removes trailing whitespace from lines
     * - Removes lines with only whitespace
     * - Ensures proper spacing around headers and sections
     * - Normalizes line endings
     *
     * @param string $content Raw markdown content
     * @return string Cleaned markdown content
     */
    private function cleanupMarkdown(string $content): string
    {
        // Normalize line endings
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        // Split into lines for processing
        $lines = explode("\n", $content);
        $cleanedLines = [];
        $consecutiveBlankLines = 0;

        foreach ($lines as $line) {
            // Remove trailing whitespace
            $line = rtrim($line);

            // Check if line is blank (empty or only whitespace)
            if (trim($line) === '') {
                $consecutiveBlankLines++;

                // Only allow maximum of 2 consecutive blank lines
                if ($consecutiveBlankLines <= 2) {
                    $cleanedLines[] = '';
                }
            } else {
                $consecutiveBlankLines = 0;
                $cleanedLines[] = $line;
            }
        }

        // Remove trailing blank lines at the end
        while (!empty($cleanedLines) && end($cleanedLines) === '') {
            array_pop($cleanedLines);
        }

        // Ensure file ends with single newline
        $cleanedLines[] = '';

        // Join lines back together
        $content = implode("\n", $cleanedLines);

        // Apply additional cleanup patterns
        $content = $this->applyMarkdownCleanupPatterns($content);

        // Format tables for better readability
        $content = $this->formatTables($content);

        return $content;
    }

    /**
     * Apply specific markdown cleanup patterns.
     *
     * @param string $content Markdown content
     * @return string Cleaned content
     */
    private function applyMarkdownCleanupPatterns(string $content): string
    {
        // First, normalize all multiple blank lines to exactly one blank line
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        // Ensure proper spacing around headers (single blank line before and after)
        // Add blank line before headers if missing (but not at start of document)
        $content = preg_replace('/(?<!\n\n)(?<!^)\n(#{1,6}\s)/', "\n\n$1", $content);

        // Add blank line after headers if missing
        $content = preg_replace('/(#{1,6}\s[^\n]+)\n(?!\n)/', "$1\n\n", $content);

        // Ensure proper spacing around code blocks
        $content = preg_replace('/(?<!\n\n)\n(```)/', "\n\n$1", $content);
        $content = preg_replace('/(```[^\n]*(?:\n(?!```)[^\n]*)*\n```)\n(?!\n)/', "$1\n\n", $content);

        // Remove blank lines that are just whitespace
        $content = preg_replace('/\n[ \t]+\n/', "\n\n", $content);

        // Remove excessive leading whitespace from list items (fix table of contents formatting)
        $content = preg_replace('/\n[ \t]{10,}(- \[)/', "\n$1", $content);

        // Fix table of contents formatting - ensure blank line after Methods heading
        $content = preg_replace('/(- \[Methods\]\(#methods\))\n(- \[)/', "$1\n\n$2", $content);

        // Handle additional whitespace variations in table of contents
        $content = preg_replace('/(- \[Methods\]\(#methods\))\n[ \t]*(- \[)/', "$1\n\n$2", $content);

        // Fix spacing issues with consecutive headers (h2 followed by h3, h4, etc.)
        $content = preg_replace('/(#{2}\s[^\n]+)\n\n\n(#{3,6}\s)/', "$1\n\n$2", $content);

        // Specific cleanup for method sections - ensure consistent spacing
        $content = preg_replace('/(\n#{4}\s[^\n]+)\n\n\n(```php)/', "$1\n\n$2", $content);

        // Final pass: ensure we never have more than one blank line anywhere
        // BUT preserve table structure by NOT adding blank lines between table rows
        $content = preg_replace('/\n{3,}/', "\n\n", $content);

        return $content;
    }

    /**
     * Format markdown tables for better readability by aligning columns.
     *
     * This method finds markdown tables and properly aligns all columns by:
     * - Calculating the maximum width for each column
     * - Padding all cells to match the maximum width
     * - Ensuring proper spacing and alignment
     *
     * @param string $content Markdown content with tables
     * @return string Content with properly formatted tables
     */
    private function formatTables(string $content): string
    {
        // Pattern to match markdown tables (header + separator + data rows)
        $tablePattern = '/(?:^|\n)((?:\|[^\n]*\|\n)+)/m';

        return preg_replace_callback($tablePattern, function($matches) {
            $tableContent = trim($matches[1]);
            $lines = explode("\n", $tableContent);

            if (count($lines) < 2) {
                return $matches[0]; // Not a valid table
            }

            // Parse all rows
            $rows = [];
            $separatorIndex = -1;

            foreach ($lines as $index => $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Check if this is a separator row (contains only |, -, :, and spaces)
                if (preg_match('/^\|[\s\-:|]+\|$/', $line)) {
                    $separatorIndex = count($rows);
                    $rows[] = $this->parseSeparatorRow($line);
                } else {
                    $rows[] = $this->parseTableRow($line);
                }
            }

            if ($separatorIndex === -1 || count($rows) < 2) {
                return $matches[0]; // Not a valid table structure
            }

            // Ensure all rows have the same number of columns
            $maxColumns = max(array_map('count', $rows));
            foreach ($rows as &$row) {
                while (count($row) < $maxColumns) {
                    $row[] = '';
                }
            }
            unset($row);

            // Calculate maximum width for each column based on actual display width
            $columnWidths = [];
            foreach ($rows as $rowIndex => $row) {
                // Skip separator row for width calculation
                if ($rowIndex === $separatorIndex) {
                    continue;
                }

                foreach ($row as $colIndex => $cell) {
                    // Calculate display width accounting for multibyte characters
                    $cellWidth = $this->calculateDisplayWidth($cell);
                    $columnWidths[$colIndex] = max($columnWidths[$colIndex] ?? 0, $cellWidth);
                }
            }

            // Ensure minimum column widths
            foreach ($columnWidths as $colIndex => $width) {
                $columnWidths[$colIndex] = max($width, 3); // Minimum 3 characters per column
            }

            // Format the table
            $formattedRows = [];
            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex === $separatorIndex) {
                    // Format separator row
                    $formattedCells = [];
                    foreach ($columnWidths as $colIndex => $width) {
                        $formattedCells[] = str_repeat('-', $width);
                    }
                    $formattedRows[] = '| ' . implode(' | ', $formattedCells) . ' |';
                } else {
                    // Format data row
                    $formattedCells = [];
                    foreach ($columnWidths as $colIndex => $width) {
                        $cellContent = $row[$colIndex] ?? '';
                        $actualDisplayWidth = $this->calculateDisplayWidth($cellContent);
                        
                        // Calculate padding: target visual width minus actual visual width
                        // Since spaces are always single-width, we can directly use this difference
                        $padding = $width - $actualDisplayWidth;
                        
                        $formattedCells[] = $cellContent . str_repeat(' ', max(0, $padding));
                    }
                    $formattedRows[] = '| ' . implode(' | ', $formattedCells) . ' |';
                }
            }

            return "\n" . implode("\n", $formattedRows) . "\n";
        }, $content);
    }

    /**
     * Parse a table row into individual cells.
     *
     * @param string $line Table row line
     * @return array Array of cell contents
     */
    private function parseTableRow(string $line): array
    {
        // Remove leading/trailing |
        $line = trim($line, '| ');

        // Split by | and trim each cell
        $cells = array_map('trim', explode('|', $line));

        return $cells;
    }

    /**
     * Parse a separator row into individual cells.
     *
     * @param string $line Separator row line
     * @return array Array of separator contents
     */
    private function parseSeparatorRow(string $line): array
    {
        // Remove leading/trailing |
        $line = trim($line, '| ');

        // Split by | and trim each cell
        $cells = array_map('trim', explode('|', $line));

        // Convert separator cells to simple dashes for processing
        return array_map(fn($cell) => str_repeat('-', max(3, strlen($cell))), $cells);
    }

    /**
     * Calculate the visual display width of text accounting for East Asian characters.
     *
     * This method calculates the actual visual width as it would appear in a monospace
     * font, where East Asian fullwidth characters (CJK) take 2 columns and other 
     * characters take 1 column. This is needed for proper table alignment in contexts
     * where visual alignment matters.
     *
     * @param string $text Text to measure
     * @return int Visual display width in columns
     */
    private function calculateDisplayWidth(string $text): int
    {
        // Convert to UTF-8 if not already
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8');
        }
        
        $width = 0;
        $length = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $codepoint = mb_ord($char, 'UTF-8');
            
            // Check if character is fullwidth (East Asian)
            if ($this->isFullwidthCharacter($codepoint)) {
                $width += 2;
            } else {
                $width += 1;
            }
        }
        
        return $width;
    }

    /**
     * Calculate character count for padding calculation.
     *
     * When adding padding spaces to align table columns, we need to know
     * the actual character count to determine how many spaces to add.
     *
     * @param string $text Text to measure
     * @return int Character count
     */
    private function calculateCharacterCount(string $text): int
    {
        // Convert to UTF-8 if not already
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8');
        }
        
        return mb_strlen($text, 'UTF-8');
    }

    /**
     * Check if a Unicode codepoint represents a fullwidth character.
     *
     * Based on Unicode East Asian Width property, fullwidth characters
     * typically take up 2 columns in fixed-width displays.
     *
     * @param int $codepoint Unicode codepoint
     * @return bool True if character is fullwidth
     */
    private function isFullwidthCharacter(int $codepoint): bool
    {
        // East Asian fullwidth ranges (common ones used in our translations)
        return (
            // CJK Unified Ideographs
            ($codepoint >= 0x4E00 && $codepoint <= 0x9FFF) ||
            // CJK Unified Ideographs Extension A
            ($codepoint >= 0x3400 && $codepoint <= 0x4DBF) ||
            // CJK Compatibility Ideographs
            ($codepoint >= 0xF900 && $codepoint <= 0xFAFF) ||
            // Hiragana
            ($codepoint >= 0x3040 && $codepoint <= 0x309F) ||
            // Katakana
            ($codepoint >= 0x30A0 && $codepoint <= 0x30FF) ||
            // Katakana Phonetic Extensions
            ($codepoint >= 0x31F0 && $codepoint <= 0x31FF) ||
            // Hangul Syllables
            ($codepoint >= 0xAC00 && $codepoint <= 0xD7AF) ||
            // CJK Symbols and Punctuation (some)
            ($codepoint >= 0x3000 && $codepoint <= 0x303F) ||
            // Fullwidth ASCII variants
            ($codepoint >= 0xFF01 && $codepoint <= 0xFF5E) ||
            // Halfwidth and Fullwidth Forms
            ($codepoint >= 0xFF00 && $codepoint <= 0xFFEF)
        );
    }

    /**
     * Strip markdown formatting to calculate actual display width.
     *
     * @param string $text Text with markdown formatting
     * @return string Text without markdown formatting
     */
    private function stripMarkdownFormatting(string $text): string
    {
        // Remove markdown links [text](url)
        $text = preg_replace('/\[([^\]]*)\]\([^)]*\)/', '$1', $text);

        // Remove backticks
        $text = str_replace('`', '', $text);

        // Remove bold/italic markers
        $text = preg_replace('/[*_]{1,2}([^*_]*)[*_]{1,2}/', '$1', $text);

        // Decode HTML entities for length calculation
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $text;
    }

    /**
     * Format a constant value for display in documentation.
     *
     * Removes unnecessary quotes from string values while preserving proper
     * formatting for other types.
     */
    private function formatConstantValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            // For simple arrays, show a formatted representation
            return '[' . implode(', ', array_map(fn($v) => $this->formatConstantValue($v), $value)) . ']';
        }

        if (is_object($value)) {
            // For enum cases or other objects, try to get a meaningful representation
            if ($value instanceof \BackedEnum) {
                return (string) $value->value;
            }

            if ($value instanceof \UnitEnum) {
                return $value->name;
            }

            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            return get_class($value);
        }

        // For numbers and other scalar types
        return (string) $value;
    }

    /**
     * Find the project root directory from the src directory path.
     */
    private function findProjectRootFromSrc(string $srcDir): string
    {
        $projectRoot = $this->findProjectRoot($srcDir);
        return $projectRoot ?? dirname($srcDir);
    }

    /**
     * Load translation data for all available locales.
     *
     * @return array<string, array<string, mixed>>
     */
    private function loadTranslationData(): array
    {
        $translationsDir = $this->projectRoot . '/translations';
        if (!is_dir($translationsDir)) {
            return [];
        }

        $translations = [];
        $translationFiles = glob($translationsDir . '/messages.*.yaml');

        if (!$translationFiles) {
            return [];
        }

        foreach ($translationFiles as $file) {
            $filename = basename($file);
            if (preg_match(DocumentationGenerator::TRANSLATION_FILE_REGEX, $filename, $matches)) {
                $locale = $matches[1];
                try {
                    $translations[$locale] = YamlParser::parseFile($file);
                } catch (\Exception $e) {
                    echo "Warning: Could not parse translation file $file: " . $e->getMessage() . "\n";
                }
            }
        }

        return $translations;
    }

    /**
     * Extract message keys from the Messages enum and organize them with their translations.
     *
     * @param array<string, array<string, mixed>> $translations
     * @return array<string, array<string, mixed>>
     */
    private function organizeMessageTranslations(array $translations): array
    {
        $organized = [];

        // Get all message keys from the Messages enum
        $messagesClass = new ReflectionClass('OpenFGA\\Messages');

        // For PHP 8.1+ enums, use getCases()
        if (method_exists($messagesClass, 'getCases')) {
            $cases = $messagesClass->getCases();
            foreach ($cases as $case) {
                $messageKey = $case->getValue()->value;
                $organized[$messageKey] = [];

                foreach ($translations as $locale => $data) {
                    $value = $this->getNestedValue($data, $messageKey);
                    if ($value !== null) {
                        $organized[$messageKey][$locale] = [
                            'displayName' => $this->getLocaleDisplayName($locale),
                            'translation' => $value
                        ];
                    }
                }
            }
        } else {
            // Fallback for older versions - get constants manually
            $enumConstants = $messagesClass->getConstants();
            foreach ($enumConstants as $constantName => $constantValue) {
                if ($constantValue instanceof \OpenFGA\Messages) {
                    $messageKey = $constantValue->value;
                    $organized[$messageKey] = [];

                    foreach ($translations as $locale => $data) {
                        $value = $this->getNestedValue($data, $messageKey);
                        if ($value !== null) {
                            $organized[$messageKey][$locale] = [
                                'displayName' => $this->getLocaleDisplayName($locale),
                                'translation' => $value
                            ];
                        }
                    }
                }
            }
        }

        return $organized;
    }

    /**
     * Get the display name for a locale code using the Language enum.
     *
     * @param string $locale The locale code (e.g., 'en', 'pt_BR')
     * @return string The display name (e.g., 'English', 'Portuguese (Brazilian)')
     */
    private function getLocaleDisplayName(string $locale): string
    {
        $language = Language::fromLocale($locale);
        
        return $language !== null ? $language->displayName() : $locale;
    }

    /**
     * Get a nested value from an array using dot notation.
     *
     * @param array<string, mixed> $array
     * @param string $key
     * @return mixed
     */
    private function getNestedValue(array $array, string $key): mixed
    {
        $keys = explode('.', $key);
        $current = $array;

        foreach ($keys as $keyPart) {
            if (!is_array($current) || !array_key_exists($keyPart, $current)) {
                return null;
            }
            $current = $current[$keyPart];
        }

        return $current;
    }

    /**
     * Generate table-of-contents README.md files for each subdirectory.
     */
    private function generateTableOfContents(): void
    {
        // echo "Generating table-of-contents README.md files...\n";

        // Build directory structure from class map
        $directoryStructure = $this->buildDirectoryStructure();

        // Generate README.md for each directory
        foreach ($directoryStructure as $directory => $classes) {
            $this->generateDirectoryReadme($directory, $classes);
        }

        // echo "Table-of-contents generation complete.\n";
    }

    /**
     * Build directory structure from class map.
     */
    private function buildDirectoryStructure(): array
    {
        $structure = [];
        $subdirectories = [];

        foreach ($this->classMap as $className => $filePath) {
            try {
                $reflection = new ReflectionClass($className);

                // Skip abstract classes that are not interfaces or enums
                if ($reflection->isAbstract() && !$reflection->isInterface() && !$reflection->isEnum()) {
                    continue;
                }

                // Get the namespace path relative to OpenFGA
                $namespace = $reflection->getNamespaceName();
                if ($namespace === 'OpenFGA') {
                    $directory = '';
                } else {
                    $directory = str_replace('OpenFGA\\', '', $namespace);
                    $directory = str_replace('\\', '/', $directory);
                }

                // Track parent directories to identify subdirectories
                $this->trackSubdirectories($directory, $subdirectories);

                // Categorize the class
                $classInfo = [
                    'name' => $reflection->getShortName(),
                    'fullName' => $className,
                    'type' => $this->getClassType($reflection),
                    'description' => $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
                    'file' => $reflection->getShortName() . '.md'
                ];

                $structure[$directory][] = $classInfo;

            } catch (\Exception $e) {
                echo "Error processing class $className for TOC: " . $e->getMessage() . "\n";
            }
        }

        // Add subdirectory information to each directory
        foreach ($structure as $directory => &$directoryData) {
            $directoryData['subdirectories'] = $subdirectories[$directory] ?? [];
        }

        // Sort classes within each directory
        foreach ($structure as $directory => &$directoryData) {
            if (isset($directoryData['subdirectories'])) {
                // If we've added subdirectories, we need to restructure
                $classes = array_filter($directoryData, function($key) {
                    return $key !== 'subdirectories';
                }, ARRAY_FILTER_USE_KEY);

                usort($classes, function($a, $b) {
                    // Sort by type first (interfaces, then classes, then enums), then by name
                    $typeOrder = ['interface' => 1, 'class' => 2, 'enum' => 3];
                    $aTypeOrder = $typeOrder[$a['type']] ?? 4;
                    $bTypeOrder = $typeOrder[$b['type']] ?? 4;

                    if ($aTypeOrder !== $bTypeOrder) {
                        return $aTypeOrder <=> $bTypeOrder;
                    }

                    return strcmp($a['name'], $b['name']);
                });

                $directoryData = [
                    'classes' => $classes,
                    'subdirectories' => $directoryData['subdirectories']
                ];
            } else {
                usort($directoryData, function($a, $b) {
                    // Sort by type first (interfaces, then classes, then enums), then by name
                    $typeOrder = ['interface' => 1, 'class' => 2, 'enum' => 3];
                    $aTypeOrder = $typeOrder[$a['type']] ?? 4;
                    $bTypeOrder = $typeOrder[$b['type']] ?? 4;

                    if ($aTypeOrder !== $bTypeOrder) {
                        return $aTypeOrder <=> $bTypeOrder;
                    }

                    return strcmp($a['name'], $b['name']);
                });
            }
        }

        return $structure;
    }

    /**
     * Track subdirectories for each parent directory.
     */
    private function trackSubdirectories(string $directory, array &$subdirectories): void
    {
        if (empty($directory)) {
            return;
        }

        $parts = explode('/', $directory);
        $currentPath = '';

        for ($i = 0; $i < count($parts); $i++) {
            $parentPath = $currentPath;
            $currentPath .= ($currentPath ? '/' : '') . $parts[$i];

            // Track this as a subdirectory of its parent
            if ($i > 0) {
                if (!isset($subdirectories[$parentPath])) {
                    $subdirectories[$parentPath] = [];
                }
                if (!in_array($parts[$i], $subdirectories[$parentPath])) {
                    $subdirectories[$parentPath][] = $parts[$i];
                }
            } else {
                // This is a top-level directory under the root
                if (!isset($subdirectories[''])) {
                    $subdirectories[''] = [];
                }
                if (!in_array($parts[0], $subdirectories[''])) {
                    $subdirectories[''][] = $parts[0];
                }
            }
        }
    }

    /**
     * Get the type of a class (interface, class, or enum).
     */
    private function getClassType(ReflectionClass $reflection): string
    {
        if ($reflection->isInterface()) {
            return 'interface';
        }
        if ($reflection->isEnum()) {
            return 'enum';
        }
        return 'class';
    }

    /**
     * Generate README.md file for a specific directory.
     */
    private function generateDirectoryReadme(string $directory, array $data): void
    {
        // Check if data has the new structure with subdirectories or old structure
        $classes = [];
        $subdirectories = [];

        if (isset($data['classes']) && isset($data['subdirectories'])) {
            // New structure with subdirectories
            $classes = $data['classes'];
            $subdirectories = $data['subdirectories'];
        } else {
            // Old structure - just an array of classes
            $classes = $data;
        }

        if (empty($classes) && empty($subdirectories)) {
            return;
        }

        // Determine output directory
        if ($directory === '') {
            $outputPath = $this->outputDir;
            $directoryName = 'OpenFGA SDK';
            $breadcrumb = '';
        } else {
            $outputPath = $this->outputDir . '/' . $directory;
            $directoryName = basename($directory);
            $breadcrumb = $this->generateBreadcrumb($directory);
        }

        // Ensure directory exists
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }

        // Group classes by type
        $groupedClasses = [
            'interface' => [],
            'class' => [],
            'enum' => []
        ];

        foreach ($classes as $classInfo) {
            if (isset($classInfo['type'])) {
                $groupedClasses[$classInfo['type']][] = $classInfo;
            }
        }

        // If we have subdirectories, add them to the structure
        if (!empty($subdirectories)) {
            $groupedClasses = [
                'classes' => $classes,
                'subdirectories' => $subdirectories
            ];
        }

        // Generate content
        $content = $this->generateReadmeContent($directoryName, $breadcrumb, $groupedClasses, $directory);

        // Write README.md file
        $readmePath = $outputPath . '/README.md';
        file_put_contents($readmePath, $content);

        // echo "Generated README.md for: " . ($directory ?: 'root') . "\n";
    }

    /**
     * Generate breadcrumb navigation for a directory.
     */
    private function generateBreadcrumb(string $directory): string
    {
        $parts = explode('/', $directory);
        $breadcrumbs = ['[API Documentation](../README.md)'];

        $currentPath = '';
        foreach ($parts as $part) {
            $currentPath .= ($currentPath ? '/' : '') . $part;
            $breadcrumbs[] = $part;
        }

        return implode(' > ', $breadcrumbs);
    }

    /**
     * Generate the content for a README.md file.
     */
    private function generateReadmeContent(string $directoryName, string $breadcrumb, array $groupedClasses, string $directory): string
    {
        $content = [];

        // Header
        $content[] = "# $directoryName";
        $content[] = '';

        // Breadcrumb (if not root)
        if (!empty($breadcrumb)) {
            $content[] = $breadcrumb;
            $content[] = '';
        }

        // Description based on directory name
        $description = $this->getDirectoryDescription($directory);
        if ($description) {
            $content[] = $description;
            $content[] = '';
        }

        // Check if we have subdirectories (from the restructured data)
        $subdirectories = [];
        $classes = [];

        if (isset($groupedClasses['classes']) && isset($groupedClasses['subdirectories'])) {
            // New structure with subdirectories
            $subdirectories = $groupedClasses['subdirectories'];
            $classes = $groupedClasses['classes'];

            // Regroup classes by type
            $groupedClasses = [
                'interface' => [],
                'class' => [],
                'enum' => []
            ];

            foreach ($classes as $classInfo) {
                $groupedClasses[$classInfo['type']][] = $classInfo;
            }
        }

        // Statistics
        $totalClasses = array_sum(array_map('count', $groupedClasses));
        $content[] = "**Total Components:** $totalClasses";
        $content[] = '';

        // Subdirectories section (if any exist)
        if (!empty($subdirectories)) {
            $content[] = "## Subdirectories";
            $content[] = '';
            $content[] = '| Directory | Description |';
            $content[] = '|-----------|-------------|';

            sort($subdirectories); // Sort alphabetically
            foreach ($subdirectories as $subdir) {
                $subdirPath = $directory ? "$directory/$subdir" : $subdir;
                $subdirDescription = $this->getDirectoryDescription($subdirPath);
                $content[] = "| [`$subdir`](./$subdir/README.md) | $subdirDescription |";
            }

            $content[] = '';
        }

        // Table of contents by type
        foreach ($groupedClasses as $type => $classes) {
            if (empty($classes)) {
                continue;
            }

            $typeTitle = ucfirst($type) . 's';
            if ($type === 'class') {
                $typeTitle = 'Classes';
            } elseif ($type === 'interface') {
                $typeTitle = 'Interfaces';
            } elseif ($type === 'enum') {
                $typeTitle = 'Enumerations';
            }

            $content[] = "## $typeTitle";
            $content[] = '';

            // Create table
            $content[] = '| Name | Description |';
            $content[] = '|------|-------------|';

            foreach ($classes as $classInfo) {
                $name = "[`{$classInfo['name']}`](./{$classInfo['file']})";
                $description = $this->truncateDescription($classInfo['description'], 100);
                $content[] = "| $name | $description |";
            }

            $content[] = '';
        }

        // Footer with navigation
        if (!empty($breadcrumb)) {
            $content[] = '---';
            $content[] = '';
            $content[] = '[← Back to API Documentation](../README.md)';
        }

        return implode("\n", $content) . "\n";
    }

    /**
     * Get a description for a directory based on its purpose.
     */
    private function getDirectoryDescription(string $directory): string
    {
        $descriptions = [
            'Authentication' => 'Authentication providers and token management for OpenFGA API access.',
            'Models' => 'Domain models representing OpenFGA entities like stores, tuples, and authorization models.',
            'Models/Collections' => 'Type-safe collections for managing groups of domain objects.',
            'Models/Enums' => 'Enumeration types for consistent value constraints across the SDK.',
            'Requests' => 'Request objects for all OpenFGA API operations.',
            'Responses' => 'Response objects containing API results and metadata.',
            'Services' => 'Business logic services that orchestrate between repositories and external systems.',
            'Repositories' => 'Data access interfaces and implementations for managing OpenFGA resources.',
            'Exceptions' => 'Exception hierarchy for type-safe error handling throughout the SDK.',
            'Results' => 'Result pattern implementation for functional error handling without exceptions.',
            'Network' => 'HTTP client abstractions, retry strategies, and low-level networking components.',
            'Observability' => 'Telemetry providers and monitoring integrations for operational visibility.',
            'Events' => 'Event system for cross-cutting concerns like logging and metrics collection.',
            'Factories' => 'Factory classes for consistent object creation and configuration.',
            'DI' => 'Dependency injection container and service provider for framework integration.',
            'Integration' => 'Framework integration helpers and service providers.',
            'Language' => 'DSL parser and transformer for human-readable authorization models.',
            'Translation' => 'Internationalization support and message translation utilities.',
            'Schemas' => 'JSON schema validation for ensuring data integrity and type safety.',
        ];

        return $descriptions[$directory] ?? '';
    }

    /**
     * Truncate description text to a reasonable length for table display.
     */
    private function truncateDescription(?string $description, int $maxLength): string
    {
        if (empty($description)) {
            return '';
        }

        // Remove newlines and excessive whitespace
        $description = preg_replace('/\s+/', ' ', trim($description));

        if (strlen($description) <= $maxLength) {
            return $description;
        }

        return substr($description, 0, $maxLength - 3) . '...';
    }

    /**
     * Generate the main API documentation index page.
     */
    private function generateMainApiIndex(): void
    {
        // echo "Generating main API documentation index...\n";

        // Build a complete component index grouped by type
        $componentIndex = [
            'interfaces' => [],
            'classes' => [],
            'enums' => []
        ];

        foreach ($this->classMap as $className => $_) {
            try {
                $reflection = new ReflectionClass($className);

                // Skip abstract classes that are not interfaces or enums
                if ($reflection->isAbstract() && !$reflection->isInterface() && !$reflection->isEnum()) {
                    continue;
                }

                $componentInfo = [
                    'name' => $reflection->getShortName(),
                    'fullName' => $className,
                    'namespace' => $reflection->getNamespaceName(),
                    'description' => $this->truncateDescription(
                        $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
                        120
                    ),
                    'link' => $this->getComponentLink($className)
                ];

                if ($reflection->isInterface()) {
                    $componentIndex['interfaces'][] = $componentInfo;
                } elseif ($reflection->isEnum()) {
                    $componentIndex['enums'][] = $componentInfo;
                } else {
                    $componentIndex['classes'][] = $componentInfo;
                }

            } catch (\Exception $e) {
                // echo "Error processing $className for API index: " . $e->getMessage() . "\n";
            }
        }

        // Sort each category by name
        foreach ($componentIndex as &$category) {
            usort($category, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        // Generate the component index table
        $allComponents = $this->generateComponentIndexTable($componentIndex);

        // Render the main API index
        $content = $this->twig->render('api-toc.twig', [
            'allComponents' => $allComponents,
            'generatedDate' => date('Y-m-d H:i:s')
        ]);

        // Clean up markdown
        $content = $this->cleanupMarkdown($content);

        // Write the main index file
        $indexPath = $this->outputDir . '/API-Index.md';
        file_put_contents($indexPath, $content);

        // echo "Main API documentation index generated at: $indexPath\n";
    }

    /**
     * Generate a markdown table for all components.
     */
    private function generateComponentIndexTable(array $componentIndex): string
    {
        $markdown = [];

        // Interfaces section
        if (!empty($componentIndex['interfaces'])) {
            $markdown[] = "### All Interfaces";
            $markdown[] = "";
            $markdown[] = "| Interface | Namespace | Description |";
            $markdown[] = "|-----------|-----------|-------------|";

            foreach ($componentIndex['interfaces'] as $component) {
                $link = "[`{$component['name']}`]({$component['link']})";
                $namespace = str_replace('OpenFGA\\', '', $component['namespace']);
                $markdown[] = "| $link | `$namespace` | {$component['description']} |";
            }

            $markdown[] = "";
        }

        // Classes section
        if (!empty($componentIndex['classes'])) {
            $markdown[] = "### All Classes";
            $markdown[] = "";
            $markdown[] = "| Class | Namespace | Description |";
            $markdown[] = "|-------|-----------|-------------|";

            foreach ($componentIndex['classes'] as $component) {
                $link = "[`{$component['name']}`]({$component['link']})";
                $namespace = str_replace('OpenFGA\\', '', $component['namespace']);
                $markdown[] = "| $link | `$namespace` | {$component['description']} |";
            }

            $markdown[] = "";
        }

        // Enums section
        if (!empty($componentIndex['enums'])) {
            $markdown[] = "### All Enumerations";
            $markdown[] = "";
            $markdown[] = "| Enum | Namespace | Description |";
            $markdown[] = "|------|-----------|-------------|";

            foreach ($componentIndex['enums'] as $component) {
                $link = "[`{$component['name']}`]({$component['link']})";
                $namespace = str_replace('OpenFGA\\', '', $component['namespace']);
                $markdown[] = "| $link | `$namespace` | {$component['description']} |";
            }

            $markdown[] = "";
        }

        return implode("\n", $markdown);
    }

    /**
     * Get the relative link path for a component.
     */
    private function getComponentLink(string $className): string
    {
        $path = str_replace('OpenFGA\\', '', $className);
        $path = str_replace('\\', '/', $path);
        return "./$path.md";
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
    $options = getopt('', ['src:', 'out:', 'clean']);

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
