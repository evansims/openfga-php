<?php

declare(strict_types=1);

/**
 * Documentation Link Checker for OpenFGA PHP SDK
 *
 * This tool validates all links in documentation files to ensure they are accessible
 * and point to the correct resources. It checks both internal and external links,
 * validates anchors, and reports broken or problematic links.
 *
 * Features:
 * - Validates HTTP/HTTPS external links
 * - Checks internal file references and anchors
 * - Validates @see references in PHPDoc
 * - Checks GitHub wiki links
 * - Supports link exclusion patterns
 *
 * Usage:
 *   php tools/link-checker.php [options] [paths...]
 *
 * Options:
 *   --external           Check external URLs (slower)
 *   --timeout=30         HTTP timeout in seconds (default: 30)
 *   --exclude=pattern    Exclude URLs matching pattern
 *   --format=json|table  Output format (default: table)
 *   --output=file        Save report to file
 *   --user-agent=string  Custom User-Agent header
 *
 * Examples:
 *   php tools/link-checker.php                    # Check all documentation
 *   php tools/link-checker.php docs/ src/        # Check specific directories
 *   php tools/link-checker.php --external        # Include external link validation
 *
 * Exit codes:
 *   0 - All links valid
 *   1 - Broken links found
 *   2 - Tool error
 */

namespace OpenFGA\Tools;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

/**
 * Link checker for documentation and source code.
 */
final class LinkChecker
{
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_USER_AGENT = 'OpenFGA-PHP-SDK-Link-Checker/1.0';
    
    private array $paths;
    private bool $checkExternal;
    private int $timeout;
    private array $excludePatterns;
    private string $outputFormat;
    private ?string $outputFile;
    private string $userAgent;
    private array $results = [];
    private array $stats = [
        'total_links' => 0,
        'valid_links' => 0,
        'broken_links' => 0,
        'skipped_links' => 0,
    ];

    public function __construct(array $options = [], array $paths = [])
    {
        $this->paths = empty($paths) ? ['docs/', 'src/', 'README.md'] : $paths;
        $this->checkExternal = $options['external'] ?? false;
        $this->timeout = (int)($options['timeout'] ?? self::DEFAULT_TIMEOUT);
        $this->excludePatterns = isset($options['exclude']) ? [$options['exclude']] : [
            'https://example.com*',
            'https://placeholder.com*',
            'http://localhost*',
        ];
        $this->outputFormat = $options['format'] ?? 'table';
        $this->outputFile = $options['output'] ?? null;
        $this->userAgent = $options['user-agent'] ?? self::DEFAULT_USER_AGENT;
    }

    public function check(): int
    {
        echo "🔗 Documentation Link Checker\n";
        echo "=============================\n\n";

        $this->scanFiles();
        $this->validateLinks();
        $this->outputResults();

        if ($this->stats['broken_links'] > 0) {
            echo "\n❌ Found {$this->stats['broken_links']} broken links\n";
            return 1;
        }

        echo "\n✅ All links are valid!\n";
        return 0;
    }

    private function scanFiles(): void
    {
        foreach ($this->paths as $path) {
            if (is_file($path)) {
                $this->extractLinksFromFile($path);
            } elseif (is_dir($path)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                $files = new RegexIterator($iterator, '/\.(md|php)$/');
                
                foreach ($files as $file) {
                    $this->extractLinksFromFile($file->getRealPath());
                }
            }
        }

        echo "📋 Found {$this->stats['total_links']} links to validate\n\n";
    }

    private function extractLinksFromFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $links = [];
        $relativePath = str_replace(getcwd() . '/', '', $filePath);

        // Extract markdown links: [text](url)
        preg_match_all('/\[([^\]]*)\]\(([^)]+)\)/', $content, $markdownMatches);
        foreach ($markdownMatches[2] as $url) {
            $links[] = trim($url);
        }

        // Extract HTML links: <a href="url">
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/', $content, $htmlMatches);
        foreach ($htmlMatches[1] as $url) {
            $links[] = trim($url);
        }

        // Extract @see references in PHPDoc
        preg_match_all('/@see\s+([^\s]+)/', $content, $seeMatches);
        foreach ($seeMatches[1] as $url) {
            $links[] = trim($url);
        }

        // Extract raw URLs (excluding those in code blocks)
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            // Skip code blocks and inline code
            if (preg_match('/^```|^    |`[^`]*`/', $line)) {
                continue;
            }
            
            preg_match_all('/https?:\/\/[^\s\)>\]]+/', $line, $urlMatches);
            foreach ($urlMatches[0] as $url) {
                $links[] = trim($url);
            }
        }

        foreach ($links as $link) {
            if (!$this->shouldExclude($link)) {
                $this->results[] = [
                    'file' => $relativePath,
                    'link' => $link,
                    'type' => $this->categorizeLink($link),
                    'status' => 'pending',
                    'message' => null,
                ];
                $this->stats['total_links']++;
            }
        }
    }

    private function validateLinks(): void
    {
        foreach ($this->results as &$result) {
            $shortLink = strlen($result['link']) > 60 ? substr($result['link'], 0, 57) . '...' : $result['link'];
            echo "Checking: " . basename($result['file']) . " → " . $shortLink . "\n";
            
            switch ($result['type']) {
                case 'external':
                    if ($this->checkExternal) {
                        $this->validateExternalLink($result);
                    } else {
                        $result['status'] = 'skipped';
                        $result['message'] = 'External link checking disabled';
                        $this->stats['skipped_links']++;
                    }
                    break;
                    
                case 'internal':
                    $this->validateInternalLink($result);
                    break;
                    
                case 'anchor':
                    $this->validateAnchorLink($result);
                    break;
                    
                default:
                    $result['status'] = 'unknown';
                    $result['message'] = 'Unknown link type';
                    $this->stats['skipped_links']++;
            }
        }
    }

    private function validateExternalLink(array &$result): void
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => $this->timeout,
                'user_agent' => $this->userAgent,
                'method' => 'HEAD',
                'ignore_errors' => true,
            ],
        ]);

        $headers = @get_headers($result['link'], true, $context);
        
        if ($headers === false) {
            $result['status'] = 'broken';
            $result['message'] = 'Failed to retrieve headers';
            $this->stats['broken_links']++;
        } else {
            $statusCode = $this->extractStatusCode($headers[0]);
            if ($statusCode >= 200 && $statusCode < 400) {
                $result['status'] = 'valid';
                $this->stats['valid_links']++;
            } else {
                $result['status'] = 'broken';
                $result['message'] = "HTTP $statusCode";
                $this->stats['broken_links']++;
            }
        }
    }

    private function validateInternalLink(array &$result): void
    {
        $link = $result['link'];
        $basePath = dirname($result['file']);
        
        // Handle relative paths
        if (!str_starts_with($link, '/')) {
            $fullPath = realpath($basePath . '/' . $link);
        } else {
            $fullPath = realpath($link);
        }

        if ($fullPath && file_exists($fullPath)) {
            $result['status'] = 'valid';
            $this->stats['valid_links']++;
        } else {
            $result['status'] = 'broken';
            $result['message'] = 'File not found';
            $this->stats['broken_links']++;
        }
    }

    private function validateAnchorLink(array &$result): void
    {
        $link = $result['link'];
        [$filePart, $anchor] = explode('#', $link, 2);
        
        // If no file part, assume current file
        if (empty($filePart)) {
            $targetFile = $result['file'];
        } else {
            $basePath = dirname($result['file']);
            if (str_starts_with($filePart, '/')) {
                $targetFile = ltrim($filePart, '/');
            } else {
                $targetFile = $basePath . '/' . $filePart;
            }
        }

        if (!file_exists($targetFile)) {
            $result['status'] = 'broken';
            $result['message'] = 'Target file not found';
            $this->stats['broken_links']++;
            return;
        }

        // Check if anchor exists in target file
        $content = file_get_contents($targetFile);
        $anchorFound = false;

        // Check for markdown headers
        if (preg_match('/^#+\s+.*' . preg_quote($anchor, '/') . '/mi', $content)) {
            $anchorFound = true;
        }
        
        // Check for HTML anchors
        if (preg_match('/<[^>]+id=["\']' . preg_quote($anchor, '/') . '["\'][^>]*>/', $content)) {
            $anchorFound = true;
        }

        if ($anchorFound) {
            $result['status'] = 'valid';
            $this->stats['valid_links']++;
        } else {
            $result['status'] = 'broken';
            $result['message'] = 'Anchor not found';
            $this->stats['broken_links']++;
        }
    }

    private function categorizeLink(string $link): string
    {
        if (str_starts_with($link, 'http://') || str_starts_with($link, 'https://')) {
            return 'external';
        }
        if (str_contains($link, '#')) {
            return 'anchor';
        }
        return 'internal';
    }

    private function shouldExclude(string $link): bool
    {
        foreach ($this->excludePatterns as $pattern) {
            if (fnmatch($pattern, $link)) {
                return true;
            }
        }
        return false;
    }

    private function extractStatusCode(string $statusLine): int
    {
        preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $statusLine, $matches);
        return isset($matches[1]) ? (int)$matches[1] : 0;
    }

    private function outputResults(): void
    {
        echo "\n📊 Link Validation Summary:\n";
        echo "  Total: {$this->stats['total_links']}\n";
        echo "  Valid: {$this->stats['valid_links']}\n";
        echo "  Broken: {$this->stats['broken_links']}\n";
        echo "  Skipped: {$this->stats['skipped_links']}\n";

        if ($this->stats['broken_links'] > 0) {
            echo "\n💥 Broken Links:\n";
            foreach ($this->results as $result) {
                if ($result['status'] === 'broken') {
                    echo "  - {$result['link']} in {$result['file']} ({$result['message']})\n";
                }
            }
        }

        if ($this->outputFile) {
            $output = match($this->outputFormat) {
                'json' => $this->formatAsJson(),
                default => $this->formatAsTable(),
            };
            file_put_contents($this->outputFile, $output);
            echo "\n📄 Report saved to: {$this->outputFile}\n";
        }
    }

    private function formatAsJson(): string
    {
        return json_encode([
            'stats' => $this->stats,
            'results' => $this->results,
            'timestamp' => date('c'),
        ], JSON_PRETTY_PRINT);
    }

    private function formatAsTable(): string
    {
        $output = "# Link Validation Report\n\n";
        $output .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $output .= "## Summary\n\n";
        $output .= "- Total links: {$this->stats['total_links']}\n";
        $output .= "- Valid links: {$this->stats['valid_links']}\n";
        $output .= "- Broken links: {$this->stats['broken_links']}\n";
        $output .= "- Skipped links: {$this->stats['skipped_links']}\n\n";

        if ($this->stats['broken_links'] > 0) {
            $output .= "## Broken Links\n\n";
            foreach ($this->results as $result) {
                if ($result['status'] === 'broken') {
                    $output .= "- `{$result['link']}` in {$result['file']} - {$result['message']}\n";
                }
            }
        }

        return $output;
    }
}

// CLI execution
if (basename($_SERVER['SCRIPT_NAME']) === 'link-checker.php') {
    $options = [];
    $paths = [];
    
    foreach ($_SERVER['argv'] as $i => $arg) {
        if ($i === 0) continue; // Skip script name
        
        if (str_starts_with($arg, '--')) {
            [$key, $value] = explode('=', substr($arg, 2), 2) + [null, true];
            $options[$key] = $value;
        } else {
            $paths[] = $arg;
        }
    }
    
    try {
        $checker = new LinkChecker($options, $paths);
        exit($checker->check());
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(2);
    }
}