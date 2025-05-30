<?php

declare(strict_types=1);

/**
 * Documentation Coverage Analyzer for OpenFGA PHP SDK
 *
 * This tool analyzes PHP source code to identify documentation gaps and ensure
 * comprehensive API documentation coverage. It's designed to align with our
 * code quality standards and linting tools (Rector, PHP-CS-Fixer).
 *
 * Features:
 * - Scans all public methods and classes
 * - Recognizes @inheritDoc as valid documentation (per CLAUDE.md guidelines)
 * - Skips @return documentation for void methods (removed by linters)
 * - Identifies missing @param, @throws tags where meaningful
 * - Generates realistic coverage reports
 * - Aligns with interface-first design patterns
 *
 * Usage:
 *   php tools/docs-coverage.php [options]
 *
 * Options:
 *   --format=json|table|markdown  Output format (default: table)
 *   --min-coverage=90            Minimum coverage threshold (default: 90)
 *   --output=file.txt            Save report to file
 *   --exclude=pattern            Exclude files matching pattern
 *
 * Exit codes:
 *   0 - Coverage meets minimum threshold
 *   1 - Coverage below threshold
 *   2 - Tool error or configuration issue
 */

namespace OpenFGA\Tools;

// Load Composer autoloader to enable reflection
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use ReflectionClass;
use ReflectionMethod;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Documentation coverage analyzer for PHP source code.
 */
final class DocumentationCoverageAnalyzer
{
    private const DEFAULT_MIN_COVERAGE = 90.0;
    private const OUTPUT_FORMATS = ['json', 'table', 'markdown'];
    
    private string $srcDir;
    private float $minCoverage;
    private string $outputFormat;
    private ?string $outputFile;
    private array $excludePatterns;
    private array $issues = [];
    private array $stats = [
        'total_classes' => 0,
        'documented_classes' => 0,
        'total_methods' => 0,
        'documented_methods' => 0,
        'total_parameters' => 0,
        'documented_parameters' => 0,
        'missing_returns' => 0,
        'missing_throws' => 0,
    ];

    public function __construct(array $options = [])
    {
        $this->srcDir = $options['src'] ?? 'src';
        $this->minCoverage = (float)($options['min-coverage'] ?? self::DEFAULT_MIN_COVERAGE);
        $this->outputFormat = $options['format'] ?? 'table';
        $this->outputFile = $options['output'] ?? null;
        $this->excludePatterns = isset($options['exclude']) ? [$options['exclude']] : [];
        
        $this->validateOptions();
    }

    public function analyze(): int
    {
        echo "🔍 Documentation Coverage Analysis\n";
        echo "==================================\n\n";

        $this->scanSourceFiles();
        $coverage = $this->calculateCoverage();
        
        echo "📊 Coverage Results:\n";
        echo "  Classes: {$this->stats['documented_classes']}/{$this->stats['total_classes']} (" . number_format($coverage['class_coverage'], 1) . "%)\n";
        echo "  Methods: {$this->stats['documented_methods']}/{$this->stats['total_methods']} (" . number_format($coverage['method_coverage'], 1) . "%)\n";
        echo "  Parameters: {$this->stats['documented_parameters']}/{$this->stats['total_parameters']} (" . number_format($coverage['parameter_coverage'], 1) . "%)\n";
        echo "  Overall: " . number_format($coverage['overall'], 1) . "%\n\n";

        if (!empty($this->issues)) {
            echo "⚠️  Documentation Issues Found:\n";
            foreach ($this->issues as $issue) {
                echo "  - {$issue}\n";
            }
            echo "\n";
        }

        $this->outputResults($coverage);

        if ($coverage['overall'] < $this->minCoverage) {
            echo "❌ Coverage " . number_format($coverage['overall'], 1) . "% is below minimum threshold {$this->minCoverage}%\n";
            return 1;
        }

        echo "✅ Coverage meets minimum threshold!\n";
        return 0;
    }

    private function validateOptions(): void
    {
        if (!is_dir($this->srcDir)) {
            throw new \InvalidArgumentException("Source directory '{$this->srcDir}' does not exist");
        }

        if (!in_array($this->outputFormat, self::OUTPUT_FORMATS, true)) {
            throw new \InvalidArgumentException("Invalid output format. Use: " . implode(', ', self::OUTPUT_FORMATS));
        }

        if ($this->minCoverage < 0 || $this->minCoverage > 100) {
            throw new \InvalidArgumentException("Minimum coverage must be between 0 and 100");
        }
    }

    private function scanSourceFiles(): void
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->srcDir));
        $phpFiles = new RegexIterator($iterator, '/\.php$/');

        foreach ($phpFiles as $file) {
            $filePath = $file->getRealPath();
            
            // Skip excluded patterns
            $skip = false;
            foreach ($this->excludePatterns as $pattern) {
                if (fnmatch($pattern, $filePath)) {
                    $skip = true;
                    break;
                }
            }
            
            if (!$skip) {
                $this->analyzeFile($filePath);
            }
        }
    }

    private function analyzeFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $relativePath = str_replace(getcwd() . '/', '', $filePath);
        
        echo "Analyzing: $relativePath\n";

        // Extract namespace and class name
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        } else {
            return; // Skip files without namespace
        }

        // Find all class/interface definitions
        preg_match_all('/(?:class|interface|trait)\s+(\w+)/', $content, $classMatches);
        
        foreach ($classMatches[1] as $className) {
            $fullClassName = $namespace . '\\' . $className;
            $this->analyzeClass($fullClassName, $relativePath);
        }
    }

    private function analyzeClass(string $className, string $filePath): void
    {
        try {
            $reflection = new ReflectionClass($className);
        } catch (\ReflectionException) {
            // Class might not be autoloadable, skip
            return;
        }

        $this->stats['total_classes']++;
        
        // Check class documentation
        $classDoc = $reflection->getDocComment();
        $hasClassDoc = $classDoc !== false && !empty(trim(str_replace(['/**', '*/', '*'], '', $classDoc)));
        
        if ($hasClassDoc) {
            $this->stats['documented_classes']++;
        } else {
            // Allow certain patterns to have minimal documentation requirements:
            // - Enum classes (generally self-documenting)
            // - Exception classes (often standard patterns)
            // - Simple value objects with clear names
            $isEnum = $reflection->isEnum();
            $isException = $reflection->isSubclassOf(\Throwable::class);
            
            if (!$isEnum && !$isException) {
                $this->issues[] = "Missing class documentation: $className in $filePath";
            } else {
                // Count as documented since these are typically self-documenting
                $this->stats['documented_classes']++;
            }
        }

        // Analyze public methods
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Skip inherited methods
            if ($method->getDeclaringClass()->getName() !== $className) {
                continue;
            }

            $this->analyzeMethod($method, $className, $filePath);
        }
    }

    private function analyzeMethod(ReflectionMethod $method, string $className, string $filePath): void
    {
        $this->stats['total_methods']++;
        
        $methodDoc = $method->getDocComment();
        $hasMethodDoc = $methodDoc !== false && !empty(trim(str_replace(['/**', '*/', '*'], '', $methodDoc)));
        
        if (!$hasMethodDoc) {
            $methodName = $method->getName();
            $this->issues[] = "Missing method documentation: $className::$methodName() in $filePath";
            return;
        }

        $this->stats['documented_methods']++;
        
        // Check if method uses @inheritDoc, which means it inherits documentation from interface/parent
        $hasInheritDoc = preg_match('/@inheritDoc/', $methodDoc);
        
        // Check parameter documentation
        foreach ($method->getParameters() as $parameter) {
            $this->stats['total_parameters']++;
            
            $paramName = $parameter->getName();
            $hasParamDoc = preg_match('/@param\s+[^\s]+\s+\$' . preg_quote($paramName, '/') . '/', $methodDoc);
            
            if ($hasParamDoc || $hasInheritDoc) {
                $this->stats['documented_parameters']++;
            } else {
                $this->issues[] = "Missing @param documentation for \${$paramName} in $className::{$method->getName()}() in $filePath";
            }
        }

        // Check return documentation - but only if it would be meaningful
        // Skip @return documentation checks for:
        // 1. void methods (useless @return void tags are removed by linters)
        // 2. __construct methods (constructors don't need @return documentation)
        // 3. methods with @inheritDoc (inherit return documentation from interface)
        if (!$hasInheritDoc && $method->getName() !== '__construct') {
            $returnType = $method->getReturnType();
            $needsReturnDoc = true;
            
            // Don't require @return documentation for void methods (Rector/PHP-CS-Fixer remove these)
            if ($returnType !== null) {
                $returnTypeName = $returnType instanceof \ReflectionNamedType 
                    ? $returnType->getName() 
                    : (string) $returnType;
                    
                if ($returnTypeName === 'void') {
                    $needsReturnDoc = false;
                }
            }
            
            if ($needsReturnDoc) {
                $hasReturnDoc = preg_match('/@return\s+/', $methodDoc);
                
                if (!$hasReturnDoc) {
                    $this->stats['missing_returns']++;
                    $this->issues[] = "Missing @return documentation for $className::{$method->getName()}() in $filePath";
                }
            }
        }

        // Check for @throws documentation if method can throw exceptions
        if (preg_match('/throw\s+new/', file_get_contents($method->getFileName() ?: ''))) {
            $hasThrowsDoc = preg_match('/@throws\s+/', $methodDoc);
            
            if (!$hasThrowsDoc && !$hasInheritDoc) {
                $this->stats['missing_throws']++;
                $this->issues[] = "Missing @throws documentation for $className::{$method->getName()}() in $filePath";
            }
        }
    }

    private function calculateCoverage(): array
    {
        $classCoverage = $this->stats['total_classes'] > 0 
            ? ($this->stats['documented_classes'] / $this->stats['total_classes']) * 100 
            : 100;
            
        $methodCoverage = $this->stats['total_methods'] > 0 
            ? ($this->stats['documented_methods'] / $this->stats['total_methods']) * 100 
            : 100;
            
        $parameterCoverage = $this->stats['total_parameters'] > 0 
            ? ($this->stats['documented_parameters'] / $this->stats['total_parameters']) * 100 
            : 100;

        $overall = ($classCoverage + $methodCoverage + $parameterCoverage) / 3;

        return [
            'class_coverage' => $classCoverage,
            'method_coverage' => $methodCoverage,
            'parameter_coverage' => $parameterCoverage,
            'overall' => $overall,
        ];
    }

    private function outputResults(array $coverage): void
    {
        $output = match($this->outputFormat) {
            'json' => $this->formatAsJson($coverage),
            'markdown' => $this->formatAsMarkdown($coverage),
            default => $this->formatAsTable($coverage),
        };

        if ($this->outputFile) {
            file_put_contents($this->outputFile, $output);
            echo "📄 Report saved to: {$this->outputFile}\n";
        } else {
            echo $output;
        }
    }

    private function formatAsTable(array $coverage): string
    {
        $output = "\n📋 Documentation Coverage Report\n";
        $output .= str_repeat("=", 50) . "\n\n";
        
        $output .= sprintf("%-20s %10s %10s %10s\n", "Metric", "Covered", "Total", "Coverage");
        $output .= str_repeat("-", 50) . "\n";
        $output .= sprintf("%-20s %10d %10d %9.1f%%\n", "Classes", $this->stats['documented_classes'], $this->stats['total_classes'], $coverage['class_coverage']);
        $output .= sprintf("%-20s %10d %10d %9.1f%%\n", "Methods", $this->stats['documented_methods'], $this->stats['total_methods'], $coverage['method_coverage']);
        $output .= sprintf("%-20s %10d %10d %9.1f%%\n", "Parameters", $this->stats['documented_parameters'], $this->stats['total_parameters'], $coverage['parameter_coverage']);
        $output .= str_repeat("-", 50) . "\n";
        $output .= sprintf("%-20s %10s %10s %9.1f%%\n", "Overall", "-", "-", $coverage['overall']);
        $output .= "\n";

        if (!empty($this->issues)) {
            $output .= "Issues to address:\n";
            $output .= "- " . implode("\n- ", array_slice($this->issues, 0, 10)) . "\n";
            if (count($this->issues) > 10) {
                $output .= "... and " . (count($this->issues) - 10) . " more issues\n";
            }
        }

        return $output;
    }

    private function formatAsJson(array $coverage): string
    {
        return json_encode([
            'coverage' => $coverage,
            'stats' => $this->stats,
            'issues' => $this->issues,
            'timestamp' => date('c'),
        ], JSON_PRETTY_PRINT);
    }

    private function formatAsMarkdown(array $coverage): string
    {
        $output = "# Documentation Coverage Report\n\n";
        $output .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "## Overall Coverage: " . number_format($coverage['overall'], 1) . "%\n\n";
        
        $output .= "| Metric | Covered | Total | Coverage |\n";
        $output .= "|--------|---------|-------|----------|\n";
        $output .= "| Classes | {$this->stats['documented_classes']} | {$this->stats['total_classes']} | " . number_format($coverage['class_coverage'], 1) . "% |\n";
        $output .= "| Methods | {$this->stats['documented_methods']} | {$this->stats['total_methods']} | " . number_format($coverage['method_coverage'], 1) . "% |\n";
        $output .= "| Parameters | {$this->stats['documented_parameters']} | {$this->stats['total_parameters']} | " . number_format($coverage['parameter_coverage'], 1) . "% |\n\n";

        if (!empty($this->issues)) {
            $output .= "## Issues to Address\n\n";
            foreach (array_slice($this->issues, 0, 20) as $issue) {
                $output .= "- $issue\n";
            }
            if (count($this->issues) > 20) {
                $output .= "\n... and " . (count($this->issues) - 20) . " more issues\n";
            }
        }

        return $output;
    }
}

// CLI execution
if (basename($_SERVER['SCRIPT_NAME']) === 'docs-coverage.php') {
    $options = [];
    
    // Parse command line arguments
    foreach ($_SERVER['argv'] as $arg) {
        if (str_starts_with($arg, '--')) {
            [$key, $value] = explode('=', substr($arg, 2), 2) + [null, true];
            $options[$key] = $value;
        }
    }
    
    try {
        $analyzer = new DocumentationCoverageAnalyzer($options);
        exit($analyzer->analyze());
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(2);
    }
}