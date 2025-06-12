<?php

declare(strict_types=1);

/**
 * OpenFGA PHP SDK Release Preparation Tool
 *
 * This tool helps prepare the repository for a new release by:
 * 1. Validating the version follows SemVer 2.0 spec exactly
 * 2. Verifying we're on the main branch and up to date with remote
 * 3. Validating the CHANGELOG has an [Unreleased] section
 * 4. Running all linters and tests
 * 5. Updating API documentation
 * 6. Updating the VERSION constant in Client.php
 * 7. Updating the CHANGELOG with the new version and date
 * 8. Prompting for user confirmation before creating release
 * 9. Creating and pushing a git tag for the new version
 * 10. Updating wiki documentation
 * 11. Creating a GitHub release draft with CHANGELOG content
 * 12. Adding a new [Unreleased] section to the CHANGELOG for future development
 *
 * Usage: php tools/release.php <version>
 * Example: php tools/release.php 1.2.3
 *
 * Valid version formats (SemVer 2.0):
 *   1.2.3           # Standard release
 *   1.2.3-alpha.1   # Pre-release
 *   1.2.3+build.456 # Build metadata
 *   1.2.3-beta.2+build.456 # Pre-release with build metadata
 *
 * Invalid version formats:
 *   v1.2.3 (no 'v' prefix allowed)
 *   1.0 (patch version required)
 *   1 (minor and patch versions required)
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
    private string $githubRepoUrl = 'https://github.com/evansims/openfga-php';

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
            $this->validateTagDoesNotExist();
            $this->validateBranchStatus();
            $this->validateChangelog();
            $this->runQualityChecks();
            $this->updateDocumentation();
            $this->updateVersionConstant();
            $this->updateChangelog();

            // Prompt for confirmation before proceeding with the release
            if (!$this->confirmRelease()) {
                echo "\n🛑 Release process canceled by user.\n";
                exit(0);
            }

            // Create and push git tag
            $this->createAndPushTag();

            // Update wiki documentation
            $this->updateWikiDocumentation();

            // Create GitHub release draft
            $this->createGitHubRelease();

            // Update CHANGELOG with new Unreleased section - this is already handled in updateChangelog()
            echo "\n✅ Release {$this->version} completed successfully!\n\n";

        } catch (Exception $e) {
            echo "\n❌ Release preparation failed: {$e->getMessage()}\n";
            exit(1);
        }
    }

    /**
     * Validate that the version follows SemVer 2.0 spec exactly.
     *
     * @link https://semver.org/ SemVer 2.0 Specification
     */
    private function validateVersion(): void
    {
        echo "🔍 Validating version format against SemVer 2.0...\n";

        // This regex follows the SemVer 2.0 spec exactly:
        // - Major.Minor.Patch are required
        // - Optional pre-release identifier (for example -alpha.1)
        // - Optional build metadata (for example +build.456)
        // - No 'v' prefix allowed
        if (!preg_match('/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/', $this->version)) {
            throw new InvalidArgumentException(
                "Invalid version format: {$this->version}.\n" .
                "Version must follow SemVer 2.0 spec (MAJOR.MINOR.PATCH[-prerelease][+buildmetadata]).\n" .
                "Examples: 1.2.3, 1.2.3-alpha.1, 1.2.3+build.456\n" .
                "Note: 'v' prefix is not allowed, and major, minor, and patch versions are required."
            );
        }

        echo "   ✓ Version format is valid: {$this->version}\n";
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
            $this->runCommand($command, "Failed to run {$name}", false);
            echo "   ✓ {$name} passed\n";
        }
    }

    /**
     * Update API documentation.
     */
    private function updateDocumentation(): void
    {
        echo "📚 Updating API documentation...\n";
        $this->runCommand('composer docs:api', 'Failed to update API documentation', false);
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
        $newUnreleasedSection = "\n\n## [Unreleased]\n\n## {$versionHeader}";
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
        $this->runCommand('composer docs:wiki', 'Failed to update wiki documentation', false);
        echo "   ✓ Wiki documentation updated\n";
    }

    /**
     * Run a shell command and handle errors.
     *
     * @param string $command Command to run
     * @param string $errorMessage Error message if the command fails
     * @param bool $captureOutput Whether to capture and return command output
     *
     * @return array|null Command output lines if $captureOutput is true, null otherwise
     */
    private function runCommand(string $command, string $errorMessage, bool $captureOutput = false): ?array
    {
        $fullCommand = "cd {$this->projectRoot} && {$command} 2>&1";

        $output = [];
        $exitCode = 0;
        exec($fullCommand, $output, $exitCode);

        if ($exitCode !== 0) {
            $outputString = implode("\n", $output);
            throw new RuntimeException("{$errorMessage}:\n{$outputString}");
        }

        return $captureOutput ? $output : null;
    }

    /**
     * Verify that the tag for this version doesn't already exist locally or remotely.
     */
    private function validateTagDoesNotExist(): void
    {
        echo "🔍 Checking if tag v{$this->version} already exists...\n";

        // Check local tags
        $tagCheck = $this->runCommand("git tag -l v{$this->version}", "Failed to check local tags", true);
        if (!empty($tagCheck) && count($tagCheck) > 0 && !empty($tagCheck[0])) {
            throw new RuntimeException(
                "Tag v{$this->version} already exists locally.\n" .
                "Please choose a different version number or delete the existing tag first.\n" .
                "To delete the local tag: git tag -d v{$this->version}"
            );
        }

        // Check remote tags
        $remoteTagCheck = $this->runCommand("git ls-remote --tags origin refs/tags/v{$this->version}", "Failed to check remote tags", true);
        if (!empty($remoteTagCheck) && count($remoteTagCheck) > 0 && !empty($remoteTagCheck[0])) {
            throw new RuntimeException(
                "Tag v{$this->version} already exists on the remote repository.\n" .
                "Please choose a different version number or coordinate with your team to remove the tag.\n" .
                "To delete a remote tag (use with caution): git push origin :refs/tags/v{$this->version}"
            );
        }

        echo "   ✓ Tag v{$this->version} does not exist\n";
    }

    /**
     * Verify that we are on the main branch and it is up to date with origin.
     */
    private function validateBranchStatus(): void
    {
        echo "🔍 Verifying branch status...\n";

        // Check if we are on the main branch
        $currentBranch = $this->runCommand('git rev-parse --abbrev-ref HEAD', 'Failed to determine current branch', true);
        if (empty($currentBranch) || $currentBranch[0] !== 'main') {
            throw new RuntimeException(
                "Not on main branch. Please switch to the main branch before creating a release.\n" .
                "Current branch: {$currentBranch[0]}"
            );
        }
        echo "   ✓ Currently on main branch\n";

        // Fetch the latest changes from origin
        echo "   📥 Fetching latest changes from remote...\n";
        $this->runCommand('git fetch origin', 'Failed to fetch from origin');

        // Check if the local main branch is up to date with origin/main
        $behindAhead = $this->runCommand('git rev-list --left-right --count origin/main...HEAD', 'Failed to check branch status', true);
        if (empty($behindAhead)) {
            throw new RuntimeException('Failed to check branch status');
        }

        list($behind, $ahead) = explode("\t", $behindAhead[0]);

        if ((int)$behind > 0) {
            throw new RuntimeException(
                "Local main branch is behind origin/main by {$behind} commit(s).\n" .
                "Please run 'git pull origin main' to update your branch before creating a release."
            );
        }

        if ((int)$ahead > 0) {
            throw new RuntimeException(
                "Local main branch is ahead of origin/main by {$ahead} commit(s).\n" .
                "Please push your changes to origin before creating a release.\n" .
                "Run 'git push origin main' to sync your changes."
            );
        }

        echo "   ✓ Branch is up to date with origin/main\n";
    }

    /**
     * Prompt the user for confirmation before proceeding with the release.
     *
     * @return bool True if the user confirms, false otherwise
     */
    private function confirmRelease(): bool
    {
        echo "\n⚠️  Ready to create release {$this->version}\n";
        echo "   The following steps will be performed:\n";
        echo "   1. Create and push git tag v{$this->version}\n";
        echo "   2. Update wiki documentation\n";
        echo "   3. Create a GitHub release draft\n\n";
        echo "   Are you sure you want to proceed? (y/n): ";

        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);

        return strtolower($line) === 'y';
    }

    /**
     * Create and push git tag for the release.
     */
    private function createAndPushTag(): void
    {
        echo "🏷️  Creating git tag v{$this->version}...\n";

        // Create the tag
        $this->runCommand("git tag v{$this->version}", "Failed to create git tag v{$this->version}");

        // Push the tag to origin
        echo "   📤 Pushing tag to remote...\n";
        $this->runCommand("git push origin v{$this->version}", "Failed to push git tag v{$this->version} to origin");

        echo "   ✓ Created and pushed git tag v{$this->version}\n";
    }

    /**
     * Create a GitHub release draft with CHANGELOG content.
     */
    private function createGitHubRelease(): void
    {
        echo "🚀 Creating GitHub release draft...\n";

        // Extract the relevant section from the CHANGELOG for the current version
        $changelog = file_get_contents($this->changelogPath);
        if ($changelog === false) {
            throw new RuntimeException('Failed to read CHANGELOG.md');
        }

        // Get the release notes content from the CHANGELOG
        $versionHeader = "## [{$this->version}]";
        $nextHeader = "## [";

        $startPos = strpos($changelog, $versionHeader);
        if ($startPos === false) {
            throw new RuntimeException("Failed to find version {$this->version} in CHANGELOG.md");
        }

        $endPos = strpos($changelog, $nextHeader, $startPos + strlen($versionHeader));
        if ($endPos === false) {
            // This might be the last version in the CHANGELOG
            $releaseNotes = substr($changelog, $startPos);
        } else {
            $releaseNotes = substr($changelog, $startPos, $endPos - $startPos);
        }

        // Trim any trailing whitespace or newlines
        $releaseNotes = trim($releaseNotes);

        // Generate the URL for creating a new GitHub release
        $releaseUrl = "{$this->githubRepoUrl}/releases/new?" . http_build_query([
            'tag' => "v{$this->version}",
            'title' => "v{$this->version}",
            'body' => $releaseNotes
        ]);

        // Open the URL in the default browser
        if (PHP_OS_FAMILY === 'Darwin') { // macOS
            $this->runCommand("open '{$releaseUrl}'", "Failed to open GitHub release page");
        } elseif (PHP_OS_FAMILY === 'Windows') {
            $this->runCommand("start {$releaseUrl}", "Failed to open GitHub release page");
        } elseif (PHP_OS_FAMILY === 'Linux') {
            $this->runCommand("xdg-open '{$releaseUrl}'", "Failed to open GitHub release page");
        }

        echo "   ✓ GitHub release draft created (browser opened)\n";
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
