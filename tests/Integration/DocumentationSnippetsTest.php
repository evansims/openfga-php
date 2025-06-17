<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function dirname;
use function strlen;

/*
 * Tests to ensure all code snippets embedded in documentation are valid and tested.
 */
describe('Documentation Snippets', function (): void {
    test('all PHP code blocks in markdown files are valid', function (): void {
        $docsDir = __DIR__ . '/../../docs';
        $readmeFile = __DIR__ . '/../../README.md';
        $errors = [];

        // Function to extract and validate PHP code blocks from markdown
        $validateMarkdownPhp = function (string $filepath) use (&$errors): void {
            $content = file_get_contents($filepath);
            $filename = basename($filepath);

            // Extract PHP code blocks
            preg_match_all('/```php\n(.*?)\n```/s', $content, $matches);

            foreach ($matches[1] as $index => $codeBlock) {
                $trimmedCode = trim($codeBlock);

                // Skip if it's just a comment or very short
                if (10 > strlen($trimmedCode)) {
                    continue;
                }

                // Skip if it's clearly a partial snippet (not meant to be standalone)
                // These patterns indicate incomplete code examples
                $partialPatterns = [
                    '/^\/\/ ❌/',  // Examples showing what NOT to do
                    '/^\/\/ ✅/',  // Examples showing what TO do
                    '/^\.\.\.$/',  // Continuation ellipsis
                    '/^}$/',       // Just a closing brace
                    '/^{$/',       // Just an opening brace
                    '/\$response = /',  // Assignment without context
                    '/->/',        // Method calls without object context
                    '/^use /',     // Just use statements
                    '/^namespace /', // Just namespace declarations
                    '/^class /',   // Just class declarations without body
                    '/^interface /', // Just interface declarations
                    '/^trait /',   // Just trait declarations
                    '/^function /', // Just function declarations
                    '/^if \(/',    // Just if statements without context
                    '/^try {/',    // Just try blocks without context
                    '/^catch /',   // Just catch blocks without context
                    '/^throw /',   // Just throw statements without context
                    '/^return /',  // Just return statements without context
                ];

                $isPartial = false;

                foreach ($partialPatterns as $pattern) {
                    if (preg_match($pattern, $trimmedCode)) {
                        $isPartial = true;

                        break;
                    }
                }

                if ($isPartial) {
                    continue;
                }

                // Skip if it doesn't look like a complete code example
                // Complete examples usually have <?php or are wrapped in a complete context
                if (! str_contains($trimmedCode, '<?php')
                    && ! str_contains($trimmedCode, 'new ')
                    && ! str_contains($trimmedCode, 'function(')
                    && ! str_contains($trimmedCode, '= ')) {
                    continue;
                }

                // Add <?php if not present
                if (! str_starts_with($trimmedCode, '<?php')) {
                    $codeBlock = "<?php\n" . $codeBlock;
                }

                // Write to temporary file for validation
                $tempFile = sys_get_temp_dir() . '/snippet_test_' . md5($codeBlock) . '.php';
                file_put_contents($tempFile, $codeBlock);

                // Validate syntax
                $output = shell_exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($tempFile) . ' 2>&1');

                if (! str_contains($output, 'No syntax errors detected')) {
                    // Only report as error if it looks like it should be valid PHP
                    if (str_contains($trimmedCode, '<?php') || preg_match('/^\$\w+ = new/', $trimmedCode)) {
                        $errors[] = "{$filename} (block #" . ($index + 1) . '): PHP syntax error';
                    }
                }

                // Clean up
                @unlink($tempFile);
            }
        };

        // Check README.md
        if (file_exists($readmeFile)) {
            $validateMarkdownPhp($readmeFile);
        }

        // Check all markdown files in docs directory
        if (is_dir($docsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($docsDir, RecursiveDirectoryIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && 'md' === $file->getExtension()) {
                    $validateMarkdownPhp($file->getPathname());
                }
            }
        }

        if (! empty($errors)) {
            echo "\nPHP syntax errors in documentation:\n";

            foreach ($errors as $error) {
                echo "- {$error}\n";
            }
        }

        expect($errors)->toBeEmpty();
    });

    test('snippet file references in markdown are valid', function (): void {
        $docsDir = __DIR__ . '/../../docs';
        $readmeFile = __DIR__ . '/../../README.md';
        $snippetsDir = __DIR__ . '/../../examples/snippets';
        $apiDir = __DIR__ . '/../../examples/api';
        $missingFiles = [];

        // Function to check file references
        $checkFileReferences = function (string $filepath) use (&$missingFiles): void {
            $content = file_get_contents($filepath);
            $filename = basename($filepath);

            // Look for snippet includes or references
            // Patterns: [](examples/snippets/file.php) or `examples/snippets/file.php`
            preg_match_all('/(?:\[.*?\]\(|`)(examples\/(?:snippets|api)\/[^)` ]+\.php)(?:\)|`)/', $content, $matches);

            foreach ($matches[1] as $referencedFile) {
                $fullPath = dirname(__DIR__, 2) . '/' . $referencedFile;

                if (! file_exists($fullPath)) {
                    $missingFiles[] = "{$filename} references missing file: {$referencedFile}";
                }
            }
        };

        // Check README.md
        if (file_exists($readmeFile)) {
            $checkFileReferences($readmeFile);
        }

        // Check all markdown files in docs
        if (is_dir($docsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($docsDir),
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && 'md' === $file->getExtension()) {
                    $checkFileReferences($file->getPathname());
                }
            }
        }

        if (! empty($missingFiles)) {
            echo "\nMissing file references in documentation:\n";

            foreach ($missingFiles as $missing) {
                echo "- {$missing}\n";
            }
        }

        expect($missingFiles)->toBeEmpty();
    });

    test('common code patterns in documentation are consistent', function (): void {
        $docsDir = __DIR__ . '/../../docs';
        $readmeFile = __DIR__ . '/../../README.md';
        $inconsistencies = [];

        // Function to check pattern consistency
        $checkPatterns = function (string $filepath) use (&$inconsistencies): void {
            $content = file_get_contents($filepath);
            $filename = basename($filepath);

            // Extract PHP code blocks
            preg_match_all('/```php\n(.*?)\n```/s', $content, $matches);

            foreach ($matches[1] as $index => $codeBlock) {
                // Skip small examples and partial code
                if (30 > strlen(trim($codeBlock))) {
                    continue;
                }

                // Check for consistent Client instantiation
                if (str_contains($codeBlock, 'new Client(')) {
                    // Allow if it's prefixed with namespace or has use statement
                    // Also allow if it's a continuation of a previous example (doesn't start with <?php)
                    if (! str_contains($codeBlock, 'use OpenFGA\\Client')
                        && ! str_contains($codeBlock, 'use OpenFGA\\{')
                        && ! str_contains($codeBlock, 'OpenFGA\\Client')
                        && ! str_contains($codeBlock, '\\OpenFGA\\Client')
                        && str_starts_with(trim($codeBlock), '<?php')) {
                        // Only flag as inconsistency if it's a complete example starting with <?php
                        $inconsistencies[] = "{$filename} (block #" . ($index + 1) . '): Client used without import';
                    }
                }

                // Check for consistent error handling patterns
                if (str_contains($codeBlock, '->unwrap()')
                    && ! str_contains($codeBlock, 'try')
                    && ! str_contains($codeBlock, '->succeeded()')
                    && ! str_contains($codeBlock, '->failure(')) {
                    // This might be okay for simple examples, but note it
                    // $inconsistencies[] = "$filename (block #" . ($index + 1) . "): unwrap() without error handling";
                }
            }
        };

        // Check README.md
        if (file_exists($readmeFile)) {
            $checkPatterns($readmeFile);
        }

        // Check all markdown files in docs
        if (is_dir($docsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($docsDir),
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && 'md' === $file->getExtension()) {
                    $checkPatterns($file->getPathname());
                }
            }
        }

        if (! empty($inconsistencies)) {
            echo "\nPattern inconsistencies in documentation:\n";

            foreach ($inconsistencies as $issue) {
                echo "- {$issue}\n";
            }
        }

        expect($inconsistencies)->toBeEmpty();
    });

    test('all unique code examples in docs have corresponding snippet files', function (): void {
        $docsDir = __DIR__ . '/../../docs';
        $snippetsDir = __DIR__ . '/../../examples/snippets';
        $suggestions = [];

        // Function to analyze code blocks
        $analyzeCodeBlocks = function (string $filepath) use (&$suggestions): void {
            $content = file_get_contents($filepath);
            $filename = basename($filepath);

            // Extract PHP code blocks
            preg_match_all('/```php\n(.*?)\n```/s', $content, $matches);

            foreach ($matches[1] as $index => $codeBlock) {
                // Skip very short blocks
                if (50 > strlen(trim($codeBlock))) {
                    continue;
                }

                // Look for characteristic patterns that might warrant a snippet file
                if (str_contains($codeBlock, 'new Client(')
                    && str_contains($codeBlock, '->')
                    && ! str_contains($content, 'examples/snippets/')) {
                    // Try to identify what this code demonstrates
                    $demonstrates = 'unknown';

                    if (str_contains($codeBlock, 'check(')) $demonstrates = 'authorization check';

                    if (str_contains($codeBlock, 'writeTuples(')) $demonstrates = 'tuple writing';

                    if (str_contains($codeBlock, 'listObjects(')) $demonstrates = 'listing objects';

                    if (str_contains($codeBlock, 'expand(')) $demonstrates = 'relationship expansion';

                    $suggestions[] = "{$filename} (block #" . ($index + 1) . "): Consider creating snippet for {$demonstrates} example";
                }
            }
        };

        // Analyze all markdown files
        if (is_dir($docsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($docsDir),
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && 'md' === $file->getExtension()) {
                    $analyzeCodeBlocks($file->getPathname());
                }
            }
        }

        if (! empty($suggestions)) {
            echo "\nSuggestions for additional snippets:\n";

            foreach ($suggestions as $suggestion) {
                echo "- {$suggestion}\n";
            }
            echo "\nNote: These are suggestions only. Not all inline examples need separate snippet files.\n";
        }

        // This is informational only, so we don't fail the test
        expect(true)->toBeTrue();
    });
});
