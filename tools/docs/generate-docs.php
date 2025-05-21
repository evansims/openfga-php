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

        foreach ($finder as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());
            if ($className) {
                $this->classMap[$className] = $file->getRealPath();
            }
        }
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
        $content = @file_get_contents($file);
        if ($content === false) {
            return null;
        }
        
        if (!preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            return null;
        }
        
        $namespace = $namespaceMatches[1];
        
        if (!preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            return null;
        }
        
        $className = $classMatches[1];
        
        return $namespace . '\\' . $className;
    }

    private function generateDocumentation(): void
    {
        foreach ($this->classMap as $className => $file) {
            $this->generateClassDocumentation($className, $file);
        }
    }

    private function generateClassDocumentation(string $className, string $file): void
    {
        $reflection = new ReflectionClass($className);
        
        // Skip abstract classes and interfaces
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return;
        }

        $classData = [
            'className' => $reflection->getShortName(),
            'namespace' => $reflection->getNamespaceName(),
            'classDescription' => $this->extractDescriptionFromDocComment($reflection->getDocComment() ?: ''),
            'interfaces' => array_map(fn($i) => $i->getName(), $reflection->getInterfaces()),
            'methods' => [],
        ];

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

        // Render and save
        $outputFile = $outputPath . '/' . $reflection->getShortName() . '.md';
        $content = $this->twig->render('documentation.twig', $classData);
        
        file_put_contents($outputFile, $content);
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
        
        foreach ($lines as $line) {
            $line = trim($line, "/* \t");
            
            if (empty($line) || str_starts_with($line, '@')) {
                continue;
            }
            
            $description[] = $line;
        }
        
        return implode(" ", $description);
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
            
            if (str_starts_with($line, "@param")) {
                $parts = preg_split('/\s+/', $line);
                if (isset($parts[2]) && $parts[2] === '$' . $paramName) {
                    $description[] = implode(' ', array_slice($parts, 3));
                    $capture = true;
                } else {
                    $capture = false;
                }
            } elseif ($capture && !empty($line) && !str_starts_with($line, '@')) {
                $description[] = $line;
            } elseif (!empty($line) && str_starts_with($line, '@')) {
                $capture = false;
            }
        }
        
        return implode(' ', $description);
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
            
            if (str_starts_with($line, "@return")) {
                $parts = preg_split('/\s+/', $line, 3);
                if (isset($parts[2])) {
                    $description[] = $parts[2];
                }
                $capture = true;
            } elseif ($capture && !empty($line) && !str_starts_with($line, '@')) {
                $description[] = $line;
            } elseif (!empty($line) && str_starts_with($line, '@')) {
                $capture = false;
            }
        }
        
        return implode(' ', $description);
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
