<?php

declare(strict_types=1);

/**
 * GitHub Workflow Security Audit Tool
 *
 * This tool validates and maintains security best practices for GitHub Actions workflows:
 * 1. Verifies all 'uses' statements are pinned to specific commit hashes
 * 2. Ensures version comments are present and accurate
 * 3. Checks for available updates and can update workflows automatically
 * 4. Optimizes network requests by preloading action information in batches
 *
 * Usage:
 *   php tools/workflow-security-audit.php [--fix] [--token=<github_token>]
 *
 * Options:
 *   --fix                 Automatically update workflows to latest versions
 *   --token=<token>       GitHub personal access token for API calls
 *   --help                Show this help message
 *
 * Authentication:
 *   Uses GitHub CLI (gh) if available and authenticated, otherwise falls back
 *   to GITHUB_TOKEN environment variable or --token parameter
 */

final class WorkflowSecurityAudit
{
    private const WORKFLOW_DIR = '.github/workflows';
    private const GITHUB_API_BASE = 'https://api.github.com';

    private bool $fixMode = false;
    private ?string $githubToken = null;
    private array $errors = [];
    private array $warnings = [];
    private array $updates = [];
    private array $actionCache = [];

    public function __construct(array $argv)
    {
        $this->parseArguments($argv);

        // Try to get GitHub token from various sources
        $this->githubToken = $this->githubToken ?? ($_ENV['GITHUB_TOKEN'] ?? null);

        // Check if gh CLI is available and authenticated
        if (!$this->githubToken && $this->isGhCliAvailable()) {
            echo "🔑 Using GitHub CLI (gh) for authentication\n";
        } elseif (!$this->githubToken) {
            $this->warning('No GitHub token provided. API rate limits may apply. Use --token=<token>, set GITHUB_TOKEN environment variable, or install/authenticate gh CLI.');
        }
    }

    public function run(): int
    {
        echo "🔍 GitHub Workflow Security Audit\n";
        echo "================================\n\n";

        if (!is_dir(self::WORKFLOW_DIR)) {
            $this->error("Workflow directory not found: " . self::WORKFLOW_DIR);
            return 1;
        }

        $workflowFiles = glob(self::WORKFLOW_DIR . '/*.yml') ?: [];
        $workflowFiles = array_merge($workflowFiles, glob(self::WORKFLOW_DIR . '/*.yaml') ?: []);

        if (empty($workflowFiles)) {
            $this->error('No workflow files found');
            return 1;
        }

        echo "Found " . count($workflowFiles) . " workflow files:\n";
        foreach ($workflowFiles as $file) {
            echo "  - " . basename($file) . "\n";
        }
        echo "\n";

        // Phase 1: Collect all unique actions to minimize API calls
        $this->preloadActionInfo($workflowFiles);

        // Phase 2: Audit each workflow file with cached data
        foreach ($workflowFiles as $workflowFile) {
            $this->auditWorkflow($workflowFile);
        }

        return $this->printSummary();
    }

    private function preloadActionInfo(array $workflowFiles): void
    {
        echo "🔄 Preloading action information to minimize API calls...\n";

        // Collect all unique actions from all workflow files
        $uniqueActions = [];
        foreach ($workflowFiles as $workflowFile) {
            $actions = $this->extractActionsFromWorkflow($workflowFile);
            $uniqueActions = array_merge($uniqueActions, $actions);
        }

        $uniqueActions = array_unique($uniqueActions);

        if (empty($uniqueActions)) {
            echo "  ℹ️  No GitHub actions found to validate\n\n";
            return;
        }

        echo "  📦 Found " . count($uniqueActions) . " unique actions to validate\n";

        // Batch load action information using available authentication
        if (!$this->githubToken && $this->isGhCliAvailable()) {
            echo "  🔑 Using GitHub CLI for batch requests\n";
            $this->batchLoadActionsViaGhCli($uniqueActions);
        } else {
            echo "  🌐 Using direct API calls for batch requests\n";
            $this->batchLoadActionsViaApi($uniqueActions);
        }

        echo "  ✅ Preloaded information for " . count($uniqueActions) . " actions\n\n";
    }

    private function extractActionsFromWorkflow(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        $actions = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (preg_match('/^\s*uses:\s*(.+)$/', $line, $matches)) {
                $usesStatement = trim($matches[1], '"\'');

                // Skip docker:// actions as they don't use GitHub releases API
                if (str_starts_with($usesStatement, 'docker://')) {
                    continue;
                }

                // Extract action name from uses statement
                if (preg_match('/^([^@]+)@.+$/', $usesStatement, $actionMatches)) {
                    $action = $actionMatches[1];

                    // Only include GitHub actions (must contain slash and not be local)
                    if (str_contains($action, '/') && !str_starts_with($action, './')) {
                        // Handle actions with subpaths (for example github/codeql-action/upload-sarif)
                        // The API is for the main repository, so extract just owner/repo
                        $parts = explode('/', $action);
                        if (count($parts) >= 2) {
                            $mainAction = $parts[0] . '/' . $parts[1];
                            $actions[] = $mainAction;
                        }
                    }
                }
            }
        }

        return $actions;
    }

    private function batchLoadActionsViaGhCli(array $actions): void
    {
        foreach ($actions as $action) {
            if (isset($this->actionCache[$action])) {
                continue; // Already cached
            }

            $result = $this->getLatestReleaseInfoViaGhCli($action);
            $this->actionCache[$action] = $result;
        }
    }

    private function batchLoadActionsViaApi(array $actions): void
    {
        foreach ($actions as $action) {
            if (isset($this->actionCache[$action])) {
                continue; // Already cached
            }

            $result = $this->getLatestReleaseInfoViaApi($action);
            $this->actionCache[$action] = $result;
        }
    }

    private function auditWorkflow(string $filePath): void
    {
        echo "🔍 Auditing " . basename($filePath) . "...\n";

        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->error("Failed to read file: $filePath");
            return;
        }

        $lines = explode("\n", $content);
        $updatedLines = $lines;
        $hasChanges = false;

        foreach ($lines as $lineNumber => $line) {
            $trimmedLine = trim($line);

            // Look for 'uses:' statements
            if (preg_match('/^\s*uses:\s*(.+)$/', $line, $matches)) {
                $usesStatement = trim($matches[1]);
                $result = $this->validateUsesStatement($usesStatement, $filePath, $lineNumber + 1, $line);

                if ($result && $this->fixMode && isset($result['updated_line'])) {
                    $updatedLines[$lineNumber] = $result['updated_line'];
                    $hasChanges = true;
                    $this->updates[] = [
                        'file' => $filePath,
                        'line' => $lineNumber + 1,
                        'old' => $usesStatement,
                        'new' => $result['updated_uses'],
                        'reason' => $result['reason']
                    ];
                }
            }
        }

        if ($hasChanges && $this->fixMode) {
            if (file_put_contents($filePath, implode("\n", $updatedLines)) !== false) {
                echo "  ✅ Updated workflow file\n";
            } else {
                $this->error("Failed to write updated file: $filePath");
            }
        }

        echo "\n";
    }

    private function validateUsesStatement(string $usesStatement, string $filePath, int $lineNumber, string $originalLine = ''): ?array
    {
        // Remove quotes if present
        $usesStatement = trim($usesStatement, '"\'');

        // Split action and version/hash
        if (!preg_match('/^([^@]+)@(.+?)(?:\s*#\s*(.+))?$/', $usesStatement, $matches)) {
            $this->error("Invalid uses statement format at {$filePath}:{$lineNumber}: $usesStatement");
            return null;
        }

        $action = $matches[1];
        $versionOrHash = $matches[2];
        $comment = $matches[3] ?? null;

        // Handle Docker actions separately - they use SHA256 hashes
        if (str_starts_with($action, 'docker://')) {
            // Check if it's pinned to a SHA256 hash (sha256: prefix + 64 character hex string)
            $isPinnedToSha256 = preg_match('/^sha256:[a-f0-9]{64}$/', $versionOrHash);

            if (!$isPinnedToSha256) {
                $this->error("Docker action not pinned to SHA256 hash at {$filePath}:{$lineNumber}: $usesStatement");
                return null;
            }

            // Docker actions are properly pinned, check if version comment is present
            if (!$comment) {
                $this->warning("Missing version comment at {$filePath}:{$lineNumber}: $usesStatement");
            }

            echo "  ✅ $action is properly pinned to SHA256 hash\n";
            return null;
        }

        // Check if it's pinned to a commit hash (40 character hex string) for GitHub actions
        $isPinnedToHash = preg_match('/^[a-f0-9]{40}$/', $versionOrHash);

        if (!$isPinnedToHash) {
            $this->error("Not pinned to commit hash at {$filePath}:{$lineNumber}: $usesStatement");

            // Try to get the latest release and its commit hash
            $latestInfo = $this->getLatestReleaseInfo($action);
            if ($latestInfo) {
                return [
                    'updated_line' => $this->buildUpdatedLine($originalLine, $action, $latestInfo['commit'], $latestInfo['tag']),
                    'updated_uses' => "{$action}@{$latestInfo['commit']} # {$latestInfo['tag']}",
                    'reason' => "Updated from version tag to pinned commit hash"
                ];
            }
            return null;
        }

        // Check if version comment is present
        if (!$comment) {
            $this->warning("Missing version comment at {$filePath}:{$lineNumber}: $usesStatement");
        }

        // Check if there's a newer version available
        $latestInfo = $this->getLatestReleaseInfo($action);
        if ($latestInfo && $latestInfo['commit'] !== $versionOrHash) {
            echo "  📊 Update available for $action:\n";
            echo "    Current: $versionOrHash" . ($comment ? " # $comment" : '') . "\n";
            echo "    Latest:  {$latestInfo['commit']} # {$latestInfo['tag']}\n";

            if ($this->fixMode) {
                return [
                    'updated_line' => $this->buildUpdatedLine($originalLine, $action, $latestInfo['commit'], $latestInfo['tag']),
                    'updated_uses' => "{$action}@{$latestInfo['commit']} # {$latestInfo['tag']}",
                    'reason' => "Updated to latest version {$latestInfo['tag']}"
                ];
            }
        } else {
            echo "  ✅ $action is up to date\n";
        }

        return null;
    }

    private function buildUpdatedLine(string $originalLine, string $action, string $commitHash, string $version): string
    {
        // Preserve the original indentation and structure
        // Match various patterns:
        // "      - uses: action/name@hash # comment"
        // "        uses: action/name@tag"
        // "      - uses: action/name@hash"
        $pattern = '/^(\s*-?\s*uses:\s*)([^@\s]+)@([^\s#]+)(\s*#.*)?(.*)$/';
        if (preg_match($pattern, $originalLine, $matches)) {
            $prefix = $matches[1];   // "      - uses: " or "        uses: "
            $oldAction = $matches[2]; // "action/name"
            $oldRef = $matches[3];   // "hash" or "tag"
            $oldComment = $matches[4] ?? ''; // " # comment" or empty
            $suffix = $matches[5] ?? ''; // any trailing content (newlines, etc.)

            return "{$prefix}{$action}@{$commitHash} # {$version}{$suffix}";
        }

        // Fallback: simple replacement preserving indentation for any uses: pattern
        if (preg_match('/^(\s*-?\s*uses:\s*)(.+)$/', $originalLine, $matches)) {
            $prefix = $matches[1];
            return "{$prefix}{$action}@{$commitHash} # {$version}";
        }

        // Last resort fallback
        return $originalLine;
    }

    private function getLatestReleaseInfo(string $action): ?array
    {
        if (!str_contains($action, '/')) {
            return null; // Not a GitHub action
        }

        // Handle docker:// actions differently - they don't use GitHub releases API
        if (str_starts_with($action, 'docker://')) {
            return null;
        }

        // For actions with subpaths, use the main repository for API calls
        $mainAction = $this->getMainActionName($action);

        // Check cache first to avoid repeated API calls
        if (isset($this->actionCache[$mainAction])) {
            return $this->actionCache[$mainAction];
        }

        // If we reach here during audit phase, data should already be preloaded
        // This is a fallback for any missed actions
        $this->warning("Action $mainAction was not preloaded, making individual API call");

        // Try gh CLI first, fall back to direct API calls
        if (!$this->githubToken && $this->isGhCliAvailable()) {
            $result = $this->getLatestReleaseInfoViaGhCli($mainAction);
            if ($result !== null) {
                $this->actionCache[$mainAction] = $result;
                return $result;
            }
        }

        // Fallback to direct API calls
        $result = $this->getLatestReleaseInfoViaApi($mainAction);
        $this->actionCache[$mainAction] = $result;
        return $result;
    }

    private function getMainActionName(string $action): string
    {
        // Handle actions with subpaths (for example github/codeql-action/upload-sarif)
        // The API is for the main repository, so extract just owner/repo
        $parts = explode('/', $action);
        if (count($parts) >= 2) {
            return $parts[0] . '/' . $parts[1];
        }

        return $action;
    }

    private function getLatestReleaseInfoViaGhCli(string $action): ?array
    {
        // Get latest release info using gh CLI
        $releaseCommand = "gh api repos/{$action}/releases/latest 2>/dev/null";
        $releaseOutput = @shell_exec($releaseCommand);

        if (!$releaseOutput) {
            return null;
        }

        $releaseData = json_decode($releaseOutput, true);
        if (!$releaseData || !isset($releaseData['tag_name'])) {
            return null;
        }

        // Get the commit hash for this tag using gh CLI
        $tagCommand = "gh api repos/{$action}/git/ref/tags/{$releaseData['tag_name']} 2>/dev/null";
        $tagOutput = @shell_exec($tagCommand);

        if (!$tagOutput) {
            return null;
        }

        $tagData = json_decode($tagOutput, true);
        if (!$tagData || !isset($tagData['object']['sha'])) {
            return null;
        }

        return [
            'tag' => $releaseData['tag_name'],
            'commit' => $tagData['object']['sha']
        ];
    }

    private function getLatestReleaseInfoViaApi(string $action): ?array
    {
        $url = self::GITHUB_API_BASE . "/repos/{$action}/releases/latest";

        $headers = [
            'User-Agent: WorkflowSecurityAudit/1.0',
            'Accept: application/vnd.github.v3+json'
        ];

        if ($this->githubToken) {
            $headers[] = "Authorization: token {$this->githubToken}";
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => 10
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $this->warning("Failed to fetch latest release info for $action");
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['tag_name'])) {
            $this->warning("Invalid response for $action latest release");
            return null;
        }

        // Get the commit hash for this tag
        $tagUrl = self::GITHUB_API_BASE . "/repos/{$action}/git/ref/tags/{$data['tag_name']}";
        $tagResponse = @file_get_contents($tagUrl, false, $context);

        if ($tagResponse === false) {
            $this->warning("Failed to fetch tag info for $action:{$data['tag_name']}");
            return null;
        }

        $tagData = json_decode($tagResponse, true);
        if (!$tagData || !isset($tagData['object']['sha'])) {
            $this->warning("Invalid tag response for $action:{$data['tag_name']}");
            return null;
        }

        return [
            'tag' => $data['tag_name'],
            'commit' => $tagData['object']['sha']
        ];
    }

    private function isGhCliAvailable(): bool
    {
        // Check if gh command exists
        $command = 'gh --version 2>/dev/null';
        $output = @shell_exec($command);

        if (!$output || !str_contains($output, 'gh version')) {
            return false;
        }

        // Check if gh is authenticated
        $authCommand = 'gh auth status 2>/dev/null';
        $authOutput = @shell_exec($authCommand);

        // gh auth status returns info about authentication or error
        return $authOutput !== null && str_contains($authOutput, 'Logged in');
    }

    private function parseArguments(array $argv): void
    {
        foreach ($argv as $arg) {
            if ($arg === '--fix') {
                $this->fixMode = true;
            } elseif (str_starts_with($arg, '--token=')) {
                $this->githubToken = substr($arg, 8);
            } elseif ($arg === '--help') {
                $this->showHelp();
                exit(0);
            }
        }
    }

    private function showHelp(): void
    {
        echo "GitHub Workflow Security Audit Tool\n";
        echo "====================================\n\n";
        echo "Usage: php tools/workflow-security-audit.php [OPTIONS]\n\n";
        echo "Options:\n";
        echo "  --fix                 Automatically update workflows to latest versions\n";
        echo "  --token=<token>       GitHub personal access token for API calls\n";
        echo "  --help                Show this help message\n\n";
        echo "Authentication (in order of preference):\n";
        echo "  1. --token=<token>    Command line token\n";
        echo "  2. GITHUB_TOKEN       Environment variable\n";
        echo "  3. gh CLI             GitHub CLI tool (if installed and authenticated)\n";
        echo "  4. Unauthenticated    Limited to 60 requests/hour\n\n";
        echo "Examples:\n";
        echo "  php tools/workflow-security-audit.php\n";
        echo "  php tools/workflow-security-audit.php --fix\n";
        echo "  php tools/workflow-security-audit.php --fix --token=ghp_xxxxxxxxxxxx\n";
        echo "  gh auth login && php tools/workflow-security-audit.php --fix\n";
    }

    private function error(string $message): void
    {
        $this->errors[] = $message;
        echo "  ❌ $message\n";
    }

    private function warning(string $message): void
    {
        $this->warnings[] = $message;
        echo "  ⚠️  $message\n";
    }

    private function printSummary(): int
    {
        echo "\n📊 Summary\n";
        echo "==========\n";

        if (!empty($this->updates)) {
            echo "🔄 Updates made (" . count($this->updates) . "):\n";
            foreach ($this->updates as $update) {
                echo "  - " . basename($update['file']) . ":{$update['line']}: {$update['reason']}\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo "⚠️  Warnings (" . count($this->warnings) . "):\n";
            foreach ($this->warnings as $warning) {
                echo "  - $warning\n";
            }
            echo "\n";
        }

        if (!empty($this->errors)) {
            echo "❌ Errors (" . count($this->errors) . "):\n";
            foreach ($this->errors as $error) {
                echo "  - $error\n";
            }
            echo "\n";
        }

        if (empty($this->errors) && empty($this->warnings)) {
            echo "✅ All workflows pass security audit!\n";
            return 0;
        }

        if (!empty($this->errors)) {
            echo "❌ Audit failed with errors. Please fix the issues above.\n";
            return 1;
        }

        echo "⚠️  Audit completed with warnings. Consider addressing them.\n";
        return 0;
    }
}

// Run the tool
if (php_sapi_name() === 'cli' && basename($_SERVER['PHP_SELF']) === 'workflow-security-audit.php') {
    $audit = new WorkflowSecurityAudit($argv);
    exit($audit->run());
}
