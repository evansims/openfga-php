<?php

declare(strict_types=1);

/**
 * OpenFGA PHP SDK Release Preparation Tool
 * 
 * This tool helps prepare the repository for a new release by:
 * 1. Validating the CHANGELOG has an [Unreleased] section
 * 2. Running all linters and tests
 * 3. Updating API documentation
 * 4. Updating the VERSION constant in Client.php
 * 5. Updating the CHANGELOG with the new version and date
 * 6. Updating wiki documentation
 * 
 * Usage: php tools/release.php <version>
 * Example: php tools/release.php 1.3.0
 */

if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

class ReleaseManager
{
    private string $projectRoot;
    private string $version;
    private string $changelogPath;
    private string $clientPath;

    public function __construct(string $projectRoot, string $version)
    {
        $this->projectRoot = rtrim($projectRoot, '/');
        $this->version = $version;
        $this->changelogPath = $this->projectRoot . '/CHANGELOG.md';
        $this->clientPath = $this->projectRoot . '/src/Client.php';
    }

    /**
     * Main execution method for the release process.
     */
    public function execute(): void
    {
        echo "🚀 Starting release preparation for version {$this->version}\n\n";

        try {
            $this->validateVersion();
            $this->validateChangelog();
            $this->runQualityChecks();
            $this->updateDocumentation();
            $this->updateVersionConstant();
            $this->updateChangelog();
            $this->updateWikiDocumentation();
            
            echo "\n✅ Release preparation completed successfully!\n";
            echo "📋 Next steps:\n";
            echo "   1. Review the changes\n";
            echo "   2. Commit the changes: git add . && git commit -m \"chore: prepare release {$this->version}\"\n";
            echo "   3. Create and push the tag: git tag v{$this->version} && git push origin v{$this->version}\n";
            echo "   4. Create the release on GitHub\n\n";
            
        } catch (Exception $e) {
            echo "\n❌ Release preparation failed: {$e->getMessage()}\n";
            exit(1);
        }
    }

    /**
     * Validate the version format.
     */
    private function validateVersion(): void
    {
        echo "🔍 Validating version format...\n";
        
        if (!preg_match('/^\d+\.\d+\.\d+(-[a-zA-Z0-9\-\.]+)?$/', $this->version)) {
            throw new InvalidArgumentException(
                "Invalid version format: {$this->version}. Expected format: x.y.z or x.y.z-suffix"
            );
        }
        
        echo "   ✓ Version format is valid\n";
    }

    /**
     * Validate that the CHANGELOG has an [Unreleased] section.
     */
    private function validateChangelog(): void
    {
        echo "📝 Validating CHANGELOG...\n";
        
        if (!file_exists($this->changelogPath)) {
            throw new RuntimeException('CHANGELOG.md not found');
        }

        $changelogContent = file_get_contents($this->changelogPath);
        if ($changelogContent === false) {
            throw new RuntimeException('Failed to read CHANGELOG.md');
        }

        if (!str_contains($changelogContent, '[Unreleased]')) {
            throw new RuntimeException(
                'CHANGELOG.md must contain an [Unreleased] section. Please update the changelog with your changes.'
            );
        }

        echo "   ✓ CHANGELOG contains [Unreleased] section\n";
    }

    /**
     * Run all quality checks (linters and tests).
     */
    private function runQualityChecks(): void
    {
        echo "🔧 Running quality checks...\n";

        $checks = [
            'Linters' => 'composer lint',
            'Unit Tests' => 'composer test:unit',
            'Integration Tests' => 'composer test:integration',
            'Contract Tests' => 'composer test:contract'
        ];

        foreach ($checks as $name => $command) {
            echo "   🏃 Running {$name}...\n";
            $this->runCommand($command, "Failed to run {$name}");
            echo "   ✓ {$name} passed\n";
        }
    }

    /**
     * Update API documentation.
     */
    private function updateDocumentation(): void
    {
        echo "📚 Updating API documentation...\n";
        $this->runCommand('composer docs:api', 'Failed to update API documentation');
        echo "   ✓ API documentation updated\n";
    }

    /**
     * Update the VERSION constant in Client.php.
     */
    private function updateVersionConstant(): void
    {
        echo "🔧 Updating VERSION constant in Client.php...\n";
        
        if (!file_exists($this->clientPath)) {
            throw new RuntimeException('src/Client.php not found');
        }

        $clientContent = file_get_contents($this->clientPath);
        if ($clientContent === false) {
            throw new RuntimeException('Failed to read src/Client.php');
        }

        // Update the VERSION constant
        $pattern = '/public const string VERSION = \'[^\']+\';/';
        $replacement = "public const string VERSION = '{$this->version}';";
        
        $updatedContent = preg_replace($pattern, $replacement, $clientContent, 1, $count);
        
        if ($count === 0) {
            throw new RuntimeException('Failed to find VERSION constant in src/Client.php');
        }

        if (file_put_contents($this->clientPath, $updatedContent) === false) {
            throw new RuntimeException('Failed to write updated src/Client.php');
        }

        echo "   ✓ VERSION constant updated to {$this->version}\n";
    }

    /**
     * Update the CHANGELOG with the new version and date.
     */
    private function updateChangelog(): void
    {
        echo "📝 Updating CHANGELOG...\n";
        
        $changelogContent = file_get_contents($this->changelogPath);
        if ($changelogContent === false) {
            throw new RuntimeException('Failed to read CHANGELOG.md');
        }

        $today = date('Y-m-d');
        
        // Replace [Unreleased] with the new version and date
        $versionHeader = "[{$this->version}] - {$today}";
        $updatedContent = str_replace('[Unreleased]', $versionHeader, $changelogContent);
        
        // Add new [Unreleased] section at the top (after "# Changelog")
        $newUnreleasedSection = "\n\n## [Unreleased]\n\n### Added\n\n### Changed\n\n### Deprecated\n\n### Removed\n\n### Fixed\n\n### Security\n\n## {$versionHeader}";
        $updatedContent = str_replace("# Changelog\n\n## {$versionHeader}", "# Changelog{$newUnreleasedSection}", $updatedContent);

        if (file_put_contents($this->changelogPath, $updatedContent) === false) {
            throw new RuntimeException('Failed to write updated CHANGELOG.md');
        }

        echo "   ✓ CHANGELOG updated with version {$this->version} and new [Unreleased] section\n";
    }

    /**
     * Update wiki documentation.
     */
    private function updateWikiDocumentation(): void
    {
        echo "📖 Updating wiki documentation...\n";
        $this->runCommand('composer docs:wiki', 'Failed to update wiki documentation');
        echo "   ✓ Wiki documentation updated\n";
    }

    /**
     * Run a shell command and handle errors.
     */
    private function runCommand(string $command, string $errorMessage): void
    {
        $fullCommand = "cd {$this->projectRoot} && {$command} 2>&1";
        
        $output = [];
        $exitCode = 0;
        exec($fullCommand, $output, $exitCode);
        
        if ($exitCode !== 0) {
            $outputString = implode("\n", $output);
            throw new RuntimeException("{$errorMessage}:\n{$outputString}");
        }
    }
}

// Main execution
if ($argc !== 2) {
    echo "Usage: php tools/release.php <version>\n";
    echo "Example: php tools/release.php 1.3.0\n";
    exit(1);
}

$version = $argv[1];
$projectRoot = dirname(__DIR__);

try {
    $releaseManager = new ReleaseManager($projectRoot, $version);
    $releaseManager->execute();
} catch (Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    exit(1);
}