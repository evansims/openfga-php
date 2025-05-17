<?php

declare(strict_types=1);

/**
 * This script helps refactor model classes to use PHP 8.2+ features.
 * It adds readonly properties and improves type safety.
 */

function processFile(string $filePath): void {
    if (!file_exists($filePath) || !is_readable($filePath)) {
        echo "Skipping unreadable file: $filePath\n";
        return;
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        echo "Failed to read file: $filePath\n";
        return;
    }
    
    $originalContent = $content;
    
    // Skip non-model files
    if (!str_contains($content, 'class') || 
        !str_contains($content, 'implements') || 
        str_contains($content, 'interface') ||
        str_contains($content, 'trait')) {
        return;
    }
    
    // 1. Add readonly to constructor properties
    $content = preg_replace_callback(
        '/public function __construct\(\s*([^)]+)\)/s',
        function($matches) {
            $params = $matches[1];
            // Skip if already has readonly
            if (str_contains($params, 'readonly')) {
                return $matches[0];
            }
            // Add readonly to each parameter
            $params = preg_replace('/(private|protected|public)(\s+\$)/', '$1 readonly$2', $params);
            return "public function __construct($params)";
        },
        $content
    );
    
    // 2. Add array shape annotations to jsonSerialize
    if (str_contains($content, 'function jsonSerialize()') && 
        !str_contains($content, '@return array<string, mixed>')) {
        $content = preg_replace(
            '/(public function jsonSerialize\(\s*\)\s*:\s*array\s*\{)(\s*return\s*array_filter\()/s',
            "$1\n        /**\n         * @return array<string, mixed>\n         */\n        $2",
            $content
        );
    }
    
    // Only write if content changed
    if ($content !== $originalContent) {
        if (file_put_contents($filePath, $content) === false) {
            echo "Failed to write file: $filePath\n";
            return;
        }
        echo "Updated: " . basename($filePath) . "\n";
    }
}

// Main execution
$modelDir = __DIR__ . '/src/Models';

if (!is_dir($modelDir)) {
    die("Error: Directory $modelDir does not exist\n");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($modelDir, \FilesystemIterator::SKIP_DOTS),
    \RecursiveIteratorIterator::LEAVES_ONLY
);

$phpFiles = new \RegexIterator($files, '/\\.php$/i');

$processed = 0;
foreach ($phpFiles as $file) {
    processFile($file->getPathname());
    $processed++;
}

echo "\nRefactoring complete! Processed $processed files.\n";
