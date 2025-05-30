<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Tools;

use ReflectionClass;

use function file_put_contents;
use function sys_get_temp_dir;
use function unlink;

describe('WorkflowSecurityAudit', function (): void {
    beforeEach(function (): void {
        $this->tempProjectDir = sys_get_temp_dir() . '/test-project-' . uniqid();
        $this->tempDir = $this->tempProjectDir . '/.github/workflows';
        $this->toolPath = __DIR__ . '/../../tools/workflow-security-audit.php';

        // Create temp directory structure
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0o755, true);
        }
    });

    afterEach(function (): void {
        // Clean up temp files
        if (is_dir($this->tempProjectDir)) {
            $removeDirectory = function (string $dir) use (&$removeDirectory): void {
                if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                        if ('.' !== $object && '..' !== $object) {
                            if (is_dir($dir . '/' . $object) && ! is_link($dir . '/' . $object)) {
                                $removeDirectory($dir . '/' . $object);
                            } else {
                                unlink($dir . '/' . $object);
                            }
                        }
                    }
                    rmdir($dir);
                }
            };
            $removeDirectory($this->tempProjectDir);
        }
    });

    it('detects unpinned actions', function (): void {
        $workflowContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Checkout
                    uses: actions/checkout@v4
                  - name: Setup PHP
                    uses: shivammathur/setup-php@v2
            YAML;

        file_put_contents($this->tempDir . '/test.yml', $workflowContent);

        // Change to temp project root to simulate project root
        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        expect($output)->toContain('Not pinned to commit hash');
        expect($output)->toContain('actions/checkout@v4');
        expect($output)->toContain('shivammathur/setup-php@v2');
    });

    it('validates properly pinned actions', function (): void {
        $workflowContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Checkout
                    uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.2
                  - name: Setup PHP
                    uses: shivammathur/setup-php@cf4cade2721270509d5b1c766ab3549210a39a2a # v2.3.3
            YAML;

        file_put_contents($this->tempDir . '/test.yml', $workflowContent);

        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        expect($output)->not->toContain('Not pinned to commit hash');
        expect($output)->toContain('is up to date');
    });

    it('detects missing version comments', function (): void {
        $workflowContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Checkout
                    uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683
            YAML;

        file_put_contents($this->tempDir . '/test.yml', $workflowContent);

        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        expect($output)->toContain('Missing version comment');
    });

    it('shows help when requested', function (): void {
        $output = shell_exec("php {$this->toolPath} --help 2>&1");

        expect($output)->toContain('GitHub Workflow Security Audit Tool');
        expect($output)->toContain('Usage:');
        expect($output)->toContain('--fix');
        expect($output)->toContain('--token');
        expect($output)->toContain('--help');
    });

    it('handles empty workflow directory gracefully', function (): void {
        $emptyProjectDir = sys_get_temp_dir() . '/empty-project-' . uniqid();
        $emptyWorkflowDir = $emptyProjectDir . '/.github/workflows';
        mkdir($emptyWorkflowDir, 0o755, true);

        $oldCwd = getcwd();
        chdir($emptyProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        // Clean up
        rmdir($emptyWorkflowDir);
        rmdir($emptyProjectDir . '/.github');
        rmdir($emptyProjectDir);

        expect($output)->toContain('No workflow files found');
    });

    it('validates workflow file patterns', function (): void {
        // Test both .yml and .yaml extensions
        $workflowContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Test
                    uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.2
            YAML;

        file_put_contents($this->tempDir . '/test.yml', $workflowContent);
        file_put_contents($this->tempDir . '/test2.yaml', $workflowContent);

        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        expect($output)->toContain('Found 2 workflow files');
        expect($output)->toContain('test.yml');
        expect($output)->toContain('test2.yaml');
    });

    it('handles invalid workflow content gracefully', function (): void {
        $invalidContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Invalid uses
                    uses: not-a-valid-action-format
            YAML;

        file_put_contents($this->tempDir . '/invalid.yml', $invalidContent);

        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        expect($output)->toContain('Invalid uses statement format');
    });

    it('detects and uses gh CLI when available', function (): void {
        $workflowContent = <<<'YAML'
            name: Test Workflow
            on: [push]
            jobs:
              test:
                runs-on: ubuntu-latest
                steps:
                  - name: Checkout
                    uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.2
            YAML;

        file_put_contents($this->tempDir . '/test.yml', $workflowContent);

        $oldCwd = getcwd();
        chdir($this->tempProjectDir);

        // Check if gh CLI is available in the test environment
        $ghAvailable = shell_exec('gh --version 2>/dev/null');
        $ghAuth = shell_exec('gh auth status 2>/dev/null');

        $output = shell_exec("php {$this->toolPath} 2>&1");

        chdir($oldCwd);

        if ($ghAvailable && $ghAuth && str_contains($ghAuth, 'Logged in')) {
            expect($output)->toContain('Using GitHub CLI (gh) for authentication');
        } else {
            // If gh is not available or not authenticated, should show warning
            expect($output)->toContain('No GitHub token provided');
        }
    });

    it('preserves proper line formatting when updating', function (): void {
        // Include the tool file to access the class
        require_once __DIR__ . '/../../tools/workflow-security-audit.php';

        // Test that buildUpdatedLine preserves indentation and structure
        $audit = new ReflectionClass('WorkflowSecurityAudit');
        $method = $audit->getMethod('buildUpdatedLine');
        $method->setAccessible(true);
        $instance = $audit->newInstanceWithoutConstructor();

        $testCases = [
            [
                'original' => '      - uses: actions/checkout@v4',
                'action' => 'actions/checkout',
                'hash' => '11bd71901bbe5b1630ceea73d27597364c9af683',
                'version' => 'v4.2.2',
                'expected' => '      - uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.2',
            ],
            [
                'original' => '        uses: shivammathur/setup-php@v2',
                'action' => 'shivammathur/setup-php',
                'hash' => 'cf4cade2721270509d5b1c766ab3549210a39a2a',
                'version' => 'v2.3.3',
                'expected' => '        uses: shivammathur/setup-php@cf4cade2721270509d5b1c766ab3549210a39a2a # v2.3.3',
            ],
            [
                'original' => '      - uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.1',
                'action' => 'actions/checkout',
                'hash' => '11bd71901bbe5b1630ceea73d27597364c9af683',
                'version' => 'v4.2.2',
                'expected' => '      - uses: actions/checkout@11bd71901bbe5b1630ceea73d27597364c9af683 # v4.2.2',
            ],
        ];

        foreach ($testCases as $test) {
            $result = $method->invoke($instance, $test['original'], $test['action'], $test['hash'], $test['version']);
            expect($result)->toBe($test['expected']);
        }
    });
});
