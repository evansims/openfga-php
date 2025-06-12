<?php

declare(strict_types=1);

/**
 * Vale Documentation Linter Tool
 *
 * This tool uses Vale to lint documentation files for style consistency.
 * It provides a standardized way to check documentation quality across
 * the OpenFGA PHP SDK project using Google and Microsoft writing style guides.
 *
 * The tool automatically excludes the docs/API directory since it contains
 * auto-generated documentation that is already covered by linting the source files.
 * It also filters out common false positives that aren't relevant for code documentation.
 *
 * Usage:
 *   php tools/docs-lint.php [path]
 *
 * Examples:
 *   php tools/docs-lint.php                    # Lint all documentation (excludes docs/API)
 *   php tools/docs-lint.php README.md          # Lint specific file
 *   php tools/docs-lint.php docs/              # Lint docs directory (excludes docs/API)
 *
 * Exit codes:
 *   0 - No issues found
 *   1 - Style issues found
 *   2 - Vale not found or configuration error
 */

/**
 * Vale documentation linter wrapper.
 */
final class ValeLinter
{
    /**
     * Default paths to lint when no arguments provided.
     */
    private const array DEFAULT_PATHS = [
        'README.md',
        'CHANGELOG.md', 
        'CODE_OF_CONDUCT.md',
        'CLAUDE.md',
        'docs/',
        'src/',
    ];

    /**
     * Vale configuration file path.
     */
    private const string VALE_CONFIG = '.vale.ini';

    /**
     * Main execution method.
     */
    public function run(array $arguments): int
    {
        echo "🔍 Vale Documentation Linter\n";
        echo "============================\n\n";

        if (!$this->checkValeInstallation()) {
            return 2;
        }

        if (!$this->checkConfiguration()) {
            return 2;
        }

        $paths = $this->determinePaths($arguments);
        $filteredPaths = $this->filterPaths($paths);
        
        echo "Linting paths: " . implode(', ', $paths) . "\n";
        
        // Show if we're excluding API docs
        if (in_array('docs/', $paths) || in_array('docs', $paths)) {
            echo "Note: Excluding docs/API directory (auto-generated content)\n";
        }
        
        echo "\n";

        return $this->executeVale($filteredPaths);
    }

    /**
     * Check if Vale is installed and accessible.
     */
    private function checkValeInstallation(): bool
    {
        $output = [];
        $returnCode = 0;
        
        exec('which vale 2>/dev/null', $output, $returnCode);
        
        if ($returnCode !== 0) {
            echo "❌ Error: Vale is not installed or not in PATH.\n";
            echo "   Please install Vale: https://vale.sh/docs/vale-cli/installation/\n";
            return false;
        }

        // Get Vale version
        exec('vale --version 2>/dev/null', $versionOutput);
        echo "✅ Vale found: " . ($versionOutput[0] ?? 'unknown version') . "\n";
        
        return true;
    }

    /**
     * Check if Vale configuration exists and is valid.
     */
    private function checkConfiguration(): bool
    {
        if (!file_exists(self::VALE_CONFIG)) {
            echo "❌ Error: Vale configuration file not found: " . self::VALE_CONFIG . "\n";
            echo "   Please create a .vale.ini configuration file.\n";
            return false;
        }

        echo "✅ Vale configuration found: " . self::VALE_CONFIG . "\n";

        // Check if styles directory exists
        $config = parse_ini_file(self::VALE_CONFIG, true);
        $stylesPath = $config['StylesPath'] ?? 'styles';
        
        if (!is_dir($stylesPath)) {
            echo "❌ Error: Styles directory not found: $stylesPath\n";
            echo "   Please ensure the styles directory exists and contains Vale style packages.\n";
            return false;
        }

        echo "✅ Styles directory found: $stylesPath\n";
        
        return true;
    }

    /**
     * Determine which paths to lint based on arguments.
     */
    private function determinePaths(array $arguments): array
    {
        // Remove script name from arguments
        array_shift($arguments);

        if (empty($arguments)) {
            return self::DEFAULT_PATHS;
        }

        // Validate provided paths exist
        $validPaths = [];
        foreach ($arguments as $path) {
            if (file_exists($path) || $this->isGlobPattern($path)) {
                $validPaths[] = $path;
            } else {
                echo "⚠️  Warning: Path not found, skipping: $path\n";
            }
        }

        return empty($validPaths) ? self::DEFAULT_PATHS : $validPaths;
    }

    /**
     * Check if a path is a glob pattern.
     */
    private function isGlobPattern(string $path): bool
    {
        return str_contains($path, '*') || str_contains($path, '?') || str_contains($path, '[');
    }

    /**
     * Execute Vale on the specified paths.
     */
    private function executeVale(array $paths): int
    {
        $pathsString = implode(' ', array_map('escapeshellarg', $paths));
        $command = "vale --config=" . escapeshellarg(self::VALE_CONFIG) . " --output=line $pathsString";
        
        echo "Executing: $command\n\n";
        
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if (empty($output)) {
            echo "✅ No documentation style issues found!\n";
            return 0;
        }

        // Filter out false positives before displaying
        $filteredOutput = $this->filterFalsePositives($output);
        $filteredCount = count($output) - count($filteredOutput);
        
        if ($filteredCount > 0) {
            echo "ℹ️  Filtered out $filteredCount false positive(s)\n\n";
        }
        
        if (empty($filteredOutput)) {
            echo "✅ No documentation style issues found (after filtering false positives)!\n";
            return 0;
        }

        // Process and display results
        $this->displayResults($filteredOutput);
        
        // Return original return code since we're just filtering display
        return $returnCode;
    }

    /**
     * Filter paths to exclude auto-generated documentation.
     */
    private function filterPaths(array $paths): array
    {
        $filteredPaths = [];
        
        foreach ($paths as $path) {
            // If the path is specifically the docs directory, replace it with individual subdirectories
            if ($path === 'docs/' || $path === 'docs') {
                // Add specific docs subdirectories, excluding API
                if (is_dir('docs')) {
                    $docsDirs = glob('docs/*', GLOB_ONLYDIR);
                    foreach ($docsDirs as $dir) {
                        // Skip the API directory as it contains auto-generated content
                        if (basename($dir) !== 'API') {
                            $filteredPaths[] = $dir . '/';
                        }
                    }
                    // Also include markdown files directly in docs/
                    $docsFiles = glob('docs/*.md');
                    $filteredPaths = array_merge($filteredPaths, $docsFiles);
                }
            } else {
                // Keep other paths as-is
                $filteredPaths[] = $path;
            }
        }
        
        return array_unique($filteredPaths);
    }

    /**
     * Filter out common false positives that are not relevant for code documentation.
     */
    private function filterFalsePositives(array $output): array
    {
        $filteredOutput = [];
        
        foreach ($output as $line) {
            // Skip if it's a false positive pattern
            if ($this->isFalsePositive($line)) {
                continue;
            }
            
            $filteredOutput[] = $line;
        }
        
        return $filteredOutput;
    }

    /**
     * Check if a Vale output line represents a false positive.
     */
    private function isFalsePositive(string $line): bool
    {
        // Common false positive patterns in code documentation
        $falsePositivePatterns = [
            // Code syntax issues - punctuation in string literals, function calls, etc.
            'implode.*Quotes.*Punctuation should be inside',
            'explode.*Quotes.*Punctuation should be inside', 
            'sprintf.*Quotes.*Punctuation should be inside',
            'preg_.*Quotes.*Punctuation should be inside',
            'Transformer\.php:174.*Quotes.*Punctuation should be inside', // Code syntax not prose
            
            // Technical terms that are appropriate in our context
            'Microsoft\.Terms.*[aA]gent.*personal digital assistant', // "agent" is correct in our context (User Agent, etc.)
            'Microsoft\.Avoid.*backend', // "backend" is appropriate technical term
            'Microsoft\.Auto.*auto-discovery', // "auto-discovery" is correct hyphenation for this technical term
            'Microsoft\.Auto.*auto-discovered',
            
            // Abbreviations and variable names
            'Spacing.*e\.D.*should have one space', // Likely abbreviations or code
            'Spacing.*e\.T.*should have one space',
            
            // Negative numbers in code (dates, ranges, etc.)
            'Microsoft\.Negative.*Form a negative number.*en dash.*hyphen',
            
            // Repetition in enum values or similar code constructs
            'Vale\.Repetition.*is repeated.*True', // Common in enums/constants
            
            // Optional plurals in technical documentation 
            'OptionalPlurals.*tuple\(s\)', // "tuple(s)" is clear in documentation
            'Plurals.*Don\'t add.*\(s\).*tuple',
            'Plurals.*Don\'t add.*\(s\).*object', // "object(s)" is clear in documentation
            'Plurals.*Don\'t add.*\(s\).*request', // "request(s)" is clear in documentation
            
            // Quotation marks in PHP code strings that are not prose
            'YamlParser\.php.*Quotes.*Punctuation should be inside', // Code syntax, not prose
            'ValidationService\.php.*sprintf.*Quotes', // Error message formatting code
            'SchemaValidator\.php.*sprintf.*Quotes', // Error message formatting code
            
            // Proper nouns in headings that should remain capitalized
            'Headings.*OpenFGA.*should use sentence-style', // OpenFGA is a proper noun
            'Headings.*CLAUDE\.md.*should use sentence-style', // CLAUDE.md is a filename
            'Headings.*DSL.*should use sentence-style', // DSL is an acronym
            'Headings.*PSR.*should use sentence-style', // PSR is an acronym
            'Headings.*PHP.*should use sentence-style', // PHP is an acronym
            'Headings.*TL;DR.*should use sentence-style', // Common abbreviation in tech docs
            
            // Technical documentation style preferences that are acceptable
            'Google\.Slang.*TL;DR', // "TL;DR" is commonly accepted in technical documentation
            'Microsoft\.DateOrder.*Always spell out', // ISO date format is acceptable in technical docs
            
            // Heading style preferences that are debatable for technical docs
            'Headings.*Setup.*should use sentence-style', // "Setup" vs "Set up" as noun
            'Headings.*Production.*should use sentence-style', // Common technical terms
            'Headings.*Client.*should use sentence-style',
            'Headings.*Testing.*should use sentence-style',
            'Headings.*API Token.*should use sentence-style', // Technical term
            'Headings.*Table of Contents.*should use sentence-style', // Standard section
            'Headings.*Quick Start.*should use sentence-style', // Common documentation section
            'Headings.*Core Concepts.*should use sentence-style', // Common documentation section
            'Headings.*Batch Operations.*should use sentence-style', // Technical term
            'Headings.*Configuration Options.*should use sentence-style', // Common section
            'Headings.*Why.*Matters.*should use sentence-style', // Common explanatory heading
            'Headings.*Basic.*Usage.*should use sentence-style', // Common tutorial heading
            'Headings.*Processing Patterns.*should use sentence-style' // Technical section
        ];
        
        foreach ($falsePositivePatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $line)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Display Vale results in a formatted way.
     */
    private function displayResults(array $output): void
    {
        $errors = 0;
        $warnings = 0;
        $suggestions = 0;
        
        foreach ($output as $line) {
            echo $line . "\n";
            
            // Count issue types
            if (str_contains($line, ':error:')) {
                $errors++;
            } elseif (str_contains($line, ':warning:')) {
                $warnings++;
            } elseif (str_contains($line, ':suggestion:')) {
                $suggestions++;
            }
        }
        
        echo "\n📊 Summary:\n";
        if ($errors > 0) {
            echo "   ❌ Errors: $errors\n";
        }
        if ($warnings > 0) {
            echo "   ⚠️  Warnings: $warnings\n";
        }
        if ($suggestions > 0) {
            echo "   💡 Suggestions: $suggestions\n";
        }
        
        echo "\n💡 Tip: Run 'vale --help' for more options and configuration details.\n";
    }
}

// Execute the linter if run directly
if (basename($_SERVER['SCRIPT_NAME']) === 'docs-lint.php') {
    $linter = new ValeLinter();
    exit($linter->run($_SERVER['argv']));
}