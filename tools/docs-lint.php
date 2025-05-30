<?php

declare(strict_types=1);

/**
 * Vale Documentation Linter Tool
 *
 * This tool uses Vale to lint documentation files for style consistency.
 * It provides a standardized way to check documentation quality across
 * the OpenFGA PHP SDK project using Google and Microsoft writing style guides.
 *
 * Usage:
 *   php tools/vale-lint.php [path]
 *
 * Examples:
 *   php tools/vale-lint.php                    # Lint all documentation
 *   php tools/vale-lint.php README.md          # Lint specific file
 *   php tools/vale-lint.php docs/              # Lint specific directory
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
        'CONTRIBUTING.md',
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
        
        echo "Linting paths: " . implode(', ', $paths) . "\n\n";

        return $this->executeVale($paths);
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

        // Process and display results
        $this->displayResults($output);
        
        // Vale returns 1 if issues found, 0 if clean
        return $returnCode;
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
if (basename($_SERVER['SCRIPT_NAME']) === 'vale-lint.php') {
    $linter = new ValeLinter();
    exit($linter->run($_SERVER['argv']));
}