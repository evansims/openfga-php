<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

// Load Composer autoloader from project root
$autoloader = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloader)) {
    die("Error: Run 'composer install' in the project root first.\n");
}

require_once $autoloader;

class LlmsGenerator
{
    private string $projectRoot;
    private string $docsDir;
    private string $apiDocsDir;
    private string $outputFile;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = rtrim($projectRoot, '/');
        $this->docsDir = $this->projectRoot . '/docs';
        $this->apiDocsDir = $this->projectRoot . '/docs/API';
        $this->outputFile = $this->projectRoot . '/llms.txt';
    }

    public function generate(): void
    {
        echo "Generating llms.txt for LLM consumption...\n";
        
        $content = $this->buildLlmsFriendlyContent();
        
        $result = file_put_contents($this->outputFile, $content);
        if ($result === false) {
            throw new RuntimeException("Failed to write llms.txt file");
        }
        
        echo "Successfully generated llms.txt (" . number_format(strlen($content)) . " characters)\n";
    }

    private function buildLlmsFriendlyContent(): string
    {
        $content = [];
        
        // Header and overview
        $content[] = $this->generateHeader();
        
        // Project overview from main README
        $content[] = $this->generateProjectOverview();
        
        // Written guides content
        $content[] = $this->generateGuidesSection();
        
        // API Reference table of contents
        $content[] = $this->generateApiReferenceSection();
        
        // Quick reference for common patterns
        $content[] = $this->generateQuickReference();
        
        return implode("\n\n", array_filter($content));
    }

    private function generateHeader(): string
    {
        return <<<'MD'
# OpenFGA PHP SDK - LLM Knowledge Base

This document contains comprehensive information about the OpenFGA PHP SDK for AI assistants and language models. It includes complete guides, API documentation references, and examples to help implement fine-grained authorization in PHP applications.

**Repository:** https://github.com/evansims/openfga-php  
**Documentation Wiki:** https://github.com/evansims/openfga-php/wiki  
**OpenFGA Documentation:** https://openfga.dev/docs  

## Key Information for AI Assistants

- **Language:** PHP 8.3+
- **Package Name:** `evansims/openfga-php`
- **Installation:** `composer require evansims/openfga-php`
- **Purpose:** Fine-grained authorization and relationship-based access control
- **Architecture:** Result pattern for error handling, PSR-7/17/18 HTTP standards
- **Testing:** `composer test` (unit, integration, contract tests)
- **Documentation Generation:** `composer docs:api`
MD;
    }

    private function generateProjectOverview(): string
    {
        // Don't include the main README.md - it has HTML formatting and badges
        // that aren't suitable for LLM consumption. The docs/README.md is better.
        return '';
    }

    private function generateGuidesSection(): string
    {
        $sections = [];
        $sections[] = "## Complete Guides and Documentation\n";
        
        // Get all guide files from docs directory (exclude API subdirectory)
        $guideFiles = [
            'README.md' => 'SDK Overview and Quick Start',
            'Introduction.md' => 'Getting Started Guide',
            'Authentication.md' => 'Authentication and Configuration',
            'Stores.md' => 'Store Management',
            'Models.md' => 'Authorization Models and DSL',
            'Tuples.md' => 'Relationship Tuples',
            'Queries.md' => 'Authorization Queries and Checks',
            'Assertions.md' => 'Testing with Assertions',
            'Results.md' => 'Result Pattern and Error Handling',
            'Observability.md' => 'Monitoring and Telemetry',
            'ServiceProvider.md' => 'Framework Integration',
        ];

        foreach ($guideFiles as $filename => $description) {
            $filePath = $this->docsDir . '/' . $filename;
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                if ($content !== false) {
                    // Adjust header levels for unified document hierarchy
                    $adjustedContent = $this->adjustHeaderLevels($content, 3);
                    $sections[] = "### {$description}\n\n{$adjustedContent}";
                }
            }
        }

        return implode("\n\n", $sections);
    }

    private function generateApiReferenceSection(): string
    {
        $sections = [];
        $sections[] = "## API Reference Documentation";
        $sections[] = "Complete API documentation is available in the GitHub wiki. Here's the organized structure with direct links:";
        
        // Core classes
        $sections[] = "### Core Classes";
        $sections[] = $this->buildApiLinks([
            'Client.md' => 'Main OpenFGA client with all operations',
            'ClientInterface.md' => 'Client interface definition',
            'Transformer.md' => 'DSL to authorization model transformer',
            'TransformerInterface.md' => 'Transformer interface',
            'Messages.md' => 'Internationalization messages',
        ]);

        // Authentication
        $sections[] = "### Authentication";
        $sections[] = $this->buildApiLinks([
            'AuthenticationInterface.md' => 'Base authentication interface',
            'TokenAuthentication.md' => 'Pre-shared key authentication',
            'ClientCredentialAuthentication.md' => 'OAuth 2.0 client credentials',
            'AccessToken.md' => 'OAuth access token management',
            'AccessTokenInterface.md' => 'Access token interface',
        ], 'Authentication/');

        // Models and Data Structures
        $sections[] = "### Models and Data Structures";
        $modelLinks = [
            'Store.md' => 'Store model for data isolation',
            'StoreInterface.md' => 'Store interface',
            'AuthorizationModel.md' => 'Authorization model with type definitions',
            'AuthorizationModelInterface.md' => 'Authorization model interface',
            'TupleKey.md' => 'Relationship tuple key (user, relation, object)',
            'TupleKeyInterface.md' => 'Tuple key interface',
            'Tuple.md' => 'Complete relationship tuple with metadata',
            'TupleInterface.md' => 'Tuple interface',
            'TypeDefinition.md' => 'Type definition with relations',
            'TypeDefinitionInterface.md' => 'Type definition interface',
            'Condition.md' => 'Conditional authorization logic',
            'ConditionInterface.md' => 'Condition interface',
        ];
        $sections[] = $this->buildApiLinks($modelLinks, 'Models/');

        // Collections
        $sections[] = "### Collections";
        $collectionLinks = [
            'TupleKeys.md' => 'Collection of tuple keys',
            'TupleKeysInterface.md' => 'Tuple keys collection interface',
            'Tuples.md' => 'Collection of tuples',
            'TuplesInterface.md' => 'Tuples collection interface',
            'TypeDefinitions.md' => 'Collection of type definitions',
            'TypeDefinitionsInterface.md' => 'Type definitions collection interface',
            'BatchCheckItems.md' => 'Batch check items collection',
            'BatchCheckItemsInterface.md' => 'Batch check items interface',
            'Stores.md' => 'Collection of stores',
            'StoresInterface.md' => 'Stores collection interface',
        ];
        $sections[] = $this->buildApiLinks($collectionLinks, 'Models/Collections/');

        // Enums
        $sections[] = "### Enumerations";
        $enumLinks = [
            'Consistency.md' => 'Consistency levels for queries',
            'SchemaVersion.md' => 'Authorization model schema versions',
            'TupleOperation.md' => 'Tuple write operations (write/delete)',
            'TypeName.md' => 'Built-in type names',
        ];
        $sections[] = $this->buildApiLinks($enumLinks, 'Models/Enums/');

        // Requests and Responses
        $sections[] = "### Requests and Responses";
        $requestResponseLinks = [
            'Requests/CheckRequest.md' => 'Authorization check request',
            'Requests/CheckRequestInterface.md' => 'Check request interface',
            'Requests/BatchCheckRequest.md' => 'Batch authorization check request',
            'Requests/BatchCheckRequestInterface.md' => 'Batch check request interface',
            'Requests/WriteTuplesRequest.md' => 'Write/delete tuples request',
            'Requests/WriteTuplesRequestInterface.md' => 'Write tuples request interface',
            'Requests/ReadTuplesRequest.md' => 'Read tuples request',
            'Requests/ReadTuplesRequestInterface.md' => 'Read tuples request interface',
            'Responses/CheckResponse.md' => 'Authorization check response',
            'Responses/CheckResponseInterface.md' => 'Check response interface',
            'Responses/BatchCheckResponse.md' => 'Batch check response',
            'Responses/BatchCheckResponseInterface.md' => 'Batch check response interface',
        ];
        $sections[] = $this->buildApiLinks($requestResponseLinks);

        // Results and Error Handling
        $sections[] = "### Results and Error Handling";
        $resultLinks = [
            'Success.md' => 'Success result wrapper',
            'SuccessInterface.md' => 'Success interface',
            'Failure.md' => 'Failure result wrapper',
            'FailureInterface.md' => 'Failure interface',
            'ResultInterface.md' => 'Base result interface',
        ];
        $sections[] = $this->buildApiLinks($resultLinks, 'Results/');

        // Exceptions
        $sections[] = "### Exceptions";
        $exceptionLinks = [
            'ClientException.md' => 'Base client exception',
            'ClientThrowable.md' => 'Client throwable interface',
            'AuthenticationException.md' => 'Authentication errors',
            'ConfigurationException.md' => 'Configuration errors',
            'NetworkException.md' => 'Network and HTTP errors',
            'SerializationException.md' => 'Serialization errors',
        ];
        $sections[] = $this->buildApiLinks($exceptionLinks, 'Exceptions/');

        // Network and Infrastructure
        $sections[] = "### Network and Infrastructure";
        $networkLinks = [
            'RequestManager.md' => 'HTTP request management',
            'RequestManagerInterface.md' => 'Request manager interface',
            'RetryHandler.md' => 'Retry logic with exponential backoff',
            'RetryHandlerInterface.md' => 'Retry handler interface',
            'CircuitBreaker.md' => 'Circuit breaker for fault tolerance',
            'CircuitBreakerInterface.md' => 'Circuit breaker interface',
            'RequestContext.md' => 'Request context and metadata',
            'RequestContextInterface.md' => 'Request context interface',
        ];
        $sections[] = $this->buildApiLinks($networkLinks, 'Network/');

        // Observability
        $sections[] = "### Observability and Monitoring";
        $observabilityLinks = [
            'TelemetryInterface.md' => 'Telemetry provider interface',
            'OpenTelemetryProvider.md' => 'OpenTelemetry integration',
            'NoOpTelemetryProvider.md' => 'No-op telemetry provider',
            'TelemetryFactory.md' => 'Telemetry provider factory',
        ];
        $sections[] = $this->buildApiLinks($observabilityLinks, 'Observability/');

        // Schema Validation
        $sections[] = "### Schema Validation";
        $schemaLinks = [
            'Schema.md' => 'JSON schema definitions',
            'SchemaInterface.md' => 'Schema interface',
            'SchemaValidator.md' => 'Schema validation logic',
            'SchemaValidatorInterface.md' => 'Schema validator interface',
            'SchemaBuilder.md' => 'Schema builder for dynamic schemas',
            'SchemaBuilderInterface.md' => 'Schema builder interface',
        ];
        $sections[] = $this->buildApiLinks($schemaLinks, 'Schemas/');

        // Translation
        $sections[] = "### Translation and Internationalization";
        $translationLinks = [
            'Translator.md' => 'Message translation service',
            'TranslatorInterface.md' => 'Translator interface',
            'YamlParser.md' => 'YAML parser for translation files',
        ];
        $sections[] = $this->buildApiLinks($translationLinks, 'Translation/');

        // Integration
        $sections[] = "### Framework Integration";
        $integrationLinks = [
            'ServiceProvider.md' => 'Laravel service provider',
        ];
        $sections[] = $this->buildApiLinks($integrationLinks, 'Integration/');

        return implode("\n\n", $sections);
    }

    private function buildApiLinks(array $links, string $basePath = ''): string
    {
        $linkList = [];
        foreach ($links as $filename => $description) {
            // Convert filename to wiki page name (remove .md extension and handle subdirectories)
            $wikiPageName = str_replace(['/', '.md'], ['-', ''], $basePath . $filename);
            // Add API- prefix for all API documentation pages
            $wikiUrl = "https://github.com/evansims/openfga-php/wiki/API-{$wikiPageName}";
            $linkList[] = "- [{$description}]({$wikiUrl})";
        }
        return implode("\n", $linkList);
    }

    private function generateQuickReference(): string
    {
        return <<<'MD'
## Quick Reference for AI Assistants

### Common Usage Patterns

#### Basic Authorization Check
```php
use OpenFGA\Client;
use OpenFGA\Models\TupleKey;

$client = new Client(url: 'https://api.fga.example');

$result = $client->check(
    store: 'store_123',
    model: 'model_456',
    tupleKey: new TupleKey('user:alice', 'viewer', 'document:readme')
);

if ($result->success()) {
    $allowed = $result->value()->getAllowed();
}
```

#### Batch Authorization Checks
```php
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Models\BatchCheckItem;

$checks = new BatchCheckItems([
    new BatchCheckItem(
        tupleKey: new TupleKey('user:alice', 'viewer', 'document:budget'),
        correlationId: 'check-1'
    ),
    new BatchCheckItem(
        tupleKey: new TupleKey('user:bob', 'editor', 'document:budget'),
        correlationId: 'check-2'
    ),
]);

$result = $client->batchCheck(
    store: 'store_123',
    model: 'model_456',
    checks: $checks
);
```

#### DSL Authorization Model
```php
$dsl = '
    model
      schema 1.1

    type user

    type document
      relations
        define owner: [user]
        define editor: [user] or owner
        define viewer: [user] or editor
';

$model = $client->dsl($dsl)->unwrap();
```

#### Writing Relationship Tuples
```php
use OpenFGA\Models\Collections\TupleKeys;

$writes = new TupleKeys([
    new TupleKey('user:alice', 'owner', 'document:budget'),
    new TupleKey('user:bob', 'viewer', 'document:budget'),
]);

$result = $client->writeTuples(
    store: 'store_123',
    model: 'model_456',
    writes: $writes
);
```

#### List Objects a User Can Access
```php
$result = $client->listObjects(
    store: 'store_123',
    model: 'model_456',
    type: 'document',
    relation: 'viewer',
    user: 'user:alice'
);

if ($result->success()) {
    $documents = $result->value()->getObjects();
}
```

### Result Pattern Usage

The SDK uses the Result pattern instead of exceptions for expected failures:

```php
// Handle results with method chaining
$documents = $client->listObjects(...)
    ->success(fn($response) => echo "Found objects!")
    ->failure(fn($error) => echo "Error: " . $error->getMessage())
    ->unwrap(); // Gets value or throws exception

// Handle results with conditionals
$result = $client->check(...);
if ($result->success()) {
    $allowed = $result->value()->getAllowed();
} else {
    $error = $result->error();
    // Handle error
}
```

### Authentication Configuration

#### Pre-shared Key
```php
use OpenFGA\Authentication\TokenAuthentication;

$auth = new TokenAuthentication('your-api-key');
$client = new Client(
    url: 'https://api.fga.example',
    authentication: $auth
);
```

#### OAuth 2.0 Client Credentials
```php
use OpenFGA\Authentication\ClientCredentialAuthentication;

$auth = new ClientCredentialAuthentication(
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    tokenUrl: 'https://auth.fga.example/oauth/token'
);
$client = new Client(
    url: 'https://api.fga.example',
    authentication: $auth
);
```

### Common Commands

- **Install:** `composer require evansims/openfga-php`
- **Run Tests:** `composer test`
- **Generate API Docs:** `composer docs:api`
- **Generate LLMs.txt:** `composer docs:llms`
- **Lint Code:** `composer lint`

### Framework Integration

#### Laravel Service Provider
```php
// In config/app.php
'providers' => [
    OpenFGA\Integration\ServiceProvider::class,
],
```

The service provider registers the OpenFGA client in the container and provides configuration through Laravel's config system.

### Key Architecture Concepts

1. **Result Pattern:** All operations return Success/Failure objects instead of throwing exceptions
2. **Interface-First Design:** Every class implements an interface for testing and flexibility
3. **Type Safety:** Full PHP 8.3+ type hints throughout
4. **PSR Compliance:** Uses PSR-7/17/18 for HTTP handling
5. **Immutable Models:** All data models are immutable value objects
6. **Collection Types:** Type-safe collections for working with multiple objects
7. **Schema Validation:** Built-in JSON schema validation for all API interactions
MD;
    }

    /**
     * Adjust markdown header levels to maintain proper hierarchy in unified document.
     * 
     * @param string $content The markdown content to adjust
     * @param int $baseLevel The base level to start headers at (for example 3 means H1 becomes H4)
     * @return string Content with adjusted header levels
     */
    private function adjustHeaderLevels(string $content, int $baseLevel): string
    {
        // Convert headers to maintain proper hierarchy
        // H1 (#) becomes H(baseLevel+1), H2 (##) becomes H(baseLevel+2), etc.
        
        $lines = explode("\n", $content);
        $adjustedLines = [];
        
        foreach ($lines as $line) {
            if (preg_match('/^(#{1,6})\s(.+)$/', $line, $matches)) {
                $currentLevel = strlen($matches[1]);
                $headerText = $matches[2];
                
                // Calculate new level: base level + current level
                $newLevel = min($baseLevel + $currentLevel, 6); // Max H6
                $newHeaderPrefix = str_repeat('#', $newLevel);
                
                $adjustedLines[] = "{$newHeaderPrefix} {$headerText}";
            } else {
                // Convert internal documentation links to wiki links
                $adjustedLines[] = $this->convertInternalLinksToWiki($line);
            }
        }
        
        return implode("\n", $adjustedLines);
    }
    
    /**
     * Convert internal documentation links to GitHub wiki links.
     * 
     * @param string $line The line to process
     * @return string Line with converted links
     */
    private function convertInternalLinksToWiki(string $line): string
    {
        // Pattern to match markdown links pointing to .md files
        $pattern = '/\[([^\]]+)\]\(([^)]+\.md)(?:#[^)]+)?\)/';
        
        return preg_replace_callback($pattern, function($matches) {
            $linkText = $matches[1];
            $linkPath = $matches[2];
            
            // Extract just the filename without path and .md extension
            $filename = basename($linkPath, '.md');
            
            // Special handling for different documentation sections
            if (strpos($linkPath, '../') !== false || strpos($linkPath, 'Getting Started/') !== false) {
                // Handle relative paths and Getting Started section
                $wikiPageName = $filename;
            } elseif (strpos($linkPath, 'Essentials/') !== false) {
                // Essentials section pages
                $wikiPageName = $filename;
            } elseif (strpos($linkPath, 'Features/') !== false) {
                // Features section pages
                $wikiPageName = $filename;
            } else {
                // Default handling
                $wikiPageName = $filename;
            }
            
            $wikiUrl = "https://github.com/evansims/openfga-php/wiki/{$wikiPageName}";
            return "[{$linkText}]({$wikiUrl})";
        }, $line);
    }
}

// CLI execution
if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $projectRoot = dirname(__DIR__);
    
    try {
        $generator = new LlmsGenerator($projectRoot);
        $generator->generate();
        echo "LLMs.txt generation completed successfully!\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}