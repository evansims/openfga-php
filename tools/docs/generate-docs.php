<?php

declare(strict_types=1);

// Load Composer autoloader
$autoloader = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloader)) {
    die("Error: Run 'composer install' in the tools/docs directory first.\n");
}

require_once $autoloader;

use OpenFGA\Client;
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
        
        // Look for either class or interface definition
        if (preg_match('/(class|interface)\s+(\w+)/', $content, $matches)) {
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

        $classData = [
            'className' => $reflection->getShortName(),
            'namespace' => $reflection->getNamespaceName(),
            'isInterface' => $isInterface,
            'classDescription' => $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
            'interfaces' => array_map(function($interface) {
                return $this->convertToMarkdownLink($interface->getName());
            }, $reflection->getInterfaces()),
            'methods' => [],
        ];
        
        // Get interface methods documentation if this is a class (not an interface)
        $interfaceMethods = [];
        if (!$isInterface) {
            $interfaceMethods = $this->getInterfaceMethodsDocumentation($reflection);
        }

        // Process methods
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->isDestructor() || $method->isStatic()) {
                continue;
            }

            if (str_starts_with($method->getName(), '__')) {
                continue;
            }

            $methodData = [
                'name' => $method->getName(),
                'signature' => $this->getMethodSignature($method),
                'description' => $this->extractDescriptionFromDocComment($method->getDocComment() ?: ''),
                'parameters' => [],
                'return' => [
                    'type' => $this->getReturnType($method),
                    'description' => $this->extractReturnDescription($method->getDocComment() ?: ''),
                ],
            ];

            // Process parameters
            foreach ($method->getParameters() as $param) {
                $methodData['parameters'][] = [
                    'name' => '$' . $param->getName(),
                    'type' => $this->getParameterType($param),
                    'description' => $this->extractParamDescription($method->getDocComment() ?: '', $param->getName()),
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
        
        // Sort methods alphabetically
        usort($classData['methods'], function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
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
    private function getInterfaceMethodsDocumentation(ReflectionClass $reflection): array
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
                        'parameters' => [],
                        'return' => [
                            'type' => $this->getReturnType($method),
                            'description' => $this->extractReturnDescription($method->getDocComment() ?: ''),
                        ],
                        'fromInterface' => $interface->getName(),
                    ];

                    // Process parameters
                    foreach ($method->getParameters() as $param) {
                        $methodData['parameters'][] = [
                            'name' => '$' . $param->getName(),
                            'type' => $this->getParameterType($param),
                            'description' => $this->extractParamDescription($method->getDocComment() ?: '', $param->getName()),
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
        foreach ($method->getParameters() as $param) {
            $paramStr = $this->getParameterType($param) . ' ';
            $paramStr .= ($param->isPassedByReference() ? '&' : '') . '$' . $param->getName();
            
            if ($param->isDefaultValueAvailable()) {
                $default = $param->getDefaultValue();
                $paramStr .= ' = ' . json_encode($default);
            }
            
            $params[] = $paramStr;
        }

        $returnType = $this->getReturnType($method);
        $returnTypeStr = $returnType ? ': ' . $returnType : '';
        
        return sprintf(
            'public function %s(%s)%s',
            $method->getName(),
            implode(', ', $params),
            $returnTypeStr
        );
    }

    private function getParameterType(ReflectionParameter $param): string
    {
        if ($param->hasType()) {
            $type = $param->getType();
            $typeStr = (string) $type;
            
            // Handle nullable types
            if ($type->allowsNull() && $typeStr !== 'mixed') {
                $typeStr = '?' . $typeStr;
            }
            
            return $this->convertToMarkdownLink($typeStr);
        }
        
        return 'mixed';
    }

    private function getReturnType(ReflectionMethod $method): string
    {
        if ($method->hasReturnType()) {
            $returnType = $method->getReturnType();
            $typeStr = (string) $returnType;
            
            // Handle nullable return types
            if ($returnType->allowsNull() && $typeStr !== 'mixed') {
                $typeStr = '?' . $typeStr;
            }
            
            return $this->convertToMarkdownLink($typeStr);
        }
        
        return '';
    }

    private function convertToMarkdownLink(string $type): string
    {
        // Handle union types
        if (str_contains($type, '|')) {
            $types = array_map('trim', explode('|', $type));
            $convertedTypes = array_map([$this, 'convertToMarkdownLink'], $types);
            return implode('|', $convertedTypes);
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
            $convertedParams = array_map([$this, 'convertToMarkdownLink'], $genericParams);
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
            // Try to find the class in the current namespace
            $currentNamespace = $this->getCurrentNamespace();
            $possibleFullName = $currentNamespace . '\\' . $type;
            if (array_key_exists($possibleFullName, $this->classMap)) {
                $isInternalClass = true;
                $fullTypeName = $possibleFullName;
                $relativePath = str_replace('OpenFGA\\', '', $possibleFullName);
                $relativePath = str_replace('\\', '/', $relativePath);
                $displayName = $type;
            }
        }

        // If it's an internal class, create a markdown link
        if ($isInternalClass) {
            $result = "[$displayName]($relativePath.md)";
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
                $description[] = $line;
            }
        }
        
        $result = trim(implode(" ", $description));
        
        // Clean up any remaining asterisks or slashes
        $result = trim($result, "*/
 \t");
        
        return $result;
    }

    private function extractParamDescription(string $docComment, string $paramName): string
    {
        if (empty($docComment)) {
            return '';
        }

        $lines = explode("\n", $docComment);
        $description = [];
        $capture = false;
        
        foreach ($lines as $line) {
            $line = trim($line, "/* \t");
            
            // Look for the @param line for this parameter
            if (preg_match('/@param\s+[^\s]+\s+\$' . preg_quote($paramName, '/') . '(?:\s+(.*))?$/', $line, $matches)) {
                if (!empty($matches[1])) {
                    $description[] = $matches[1];
                }
                $capture = true;
            } 
            // If we're capturing and hit another tag, stop
            elseif ($capture && str_starts_with($line, '@')) {
                $capture = false;
            }
            // If we're capturing and the line isn't empty, add it to the description
            elseif ($capture && !empty($line)) {
                $description[] = $line;
            }
        }
        
        return trim(implode(' ', $description));
    }

    private function extractReturnDescription(string $docComment): string
    {
        if (empty($docComment)) {
            return '';
        }

        $lines = explode("\n", $docComment);
        $description = [];
        $capture = false;
        
        foreach ($lines as $line) {
            $line = trim($line, "/* \t");
            
            // Skip empty lines and the opening /**
            if (empty($line) || $line === '/**') {
                continue;
            }
            
            // Look for the @return line
            if (preg_match('/@return\s+[^\s]+(?:\s+(.*))?$/', $line, $matches)) {
                if (!empty($matches[1])) {
                    $description[] = $matches[1];
                }
                $capture = true;
            } 
            // If we're capturing and hit another tag, stop
            elseif ($capture && str_starts_with($line, '@')) {
                $capture = false;
            }
            // If we're capturing and the line isn't empty, add it to the description
            elseif ($capture && !empty($line)) {
                $description[] = $line;
            }
        }
        
        return trim(implode(' ', $description));
    }
}

// Run the generator
$srcDir = __DIR__ . '/../../src';
$outputDir = __DIR__ . '/../../docs/API';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$generator = new DocumentationGenerator($srcDir, $outputDir);
$generator->generate();

echo "Documentation generated successfully!\n";
