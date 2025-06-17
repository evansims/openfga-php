# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

### Release process

- `composer release` - Perform a release; pass the intended version as the first argument, such as "composer release 1.2.0"

### Testing

- `composer test` - Run all our tests (unit, integration, contract)
- `composer test:unit` - Run unit tests with coverage
- `composer test:integration` - Run integration tests (requires Docker containers: `composer integration:start`)
- `composer test:contract` - Run OpenAPI contract tests
- `composer test:tools` - Run tools tests
- Run a single test: `./vendor/bin/pest tests/Unit/Path/To/Test.php --filter="test name"`

If PEST exits with code 255, it means there is a syntax error in the test or codebase that needs fixed. NEVER consider your task complete until this issue is resolved. Think carefully about any recent changes you've made when trying to identify the cause and when considering solutions, as tasks will almost never be given while this issue is in place.

### Code quality

- `composer lint` - Run all our linters (PHPStan, Psalm, Rector, PHP-CS-Fixer)
- `composer lint:fix` - Run the linter automatic fixers (PHPStan and Rector)
- `composer lint:phpcs` - Run PHP-CS-Fixer code style analysis
- `composer lint:phpcs:fix` - Run PHP-CS-Fixer fixer
- `composer lint:phpstan` - Run PHPSTan static analysis at max level
- `composer lint:psalm` - Run Psalm static analysis at max level
- `composer lint:rector` - Run Rector static analysis
- `composer lint:rector:fix` - Run Rector fixer

### Documentation

- `composer docs` - Run common documentation checks (coverage, style, links)
- `composer docs:api` - Regenerate API documentation from code
- `composer docs:llms` - Generate LLM-friendly llms.txt with complete guides and API references
- `composer docs:wiki` - Regenerate and upload the GitHub repository's wiki documentation from code
- `composer docs:coverage` - Run documentation coverage checks
- `composer docs:links` - Run documentation link checks
- `composer docs:metrics` - Run documentation metrics checks
- `composer docs:lint` - Lint documentation files for style consistency using Vale with Google and Microsoft style guides

### Security

- `composer security:workflows` - Run security workflows
- `composer security:workflows:fix` - Run security workflows fixer

## Architecture overview

This is an OpenFGA PHP SDK implementing relationship-based access control. Key architectural patterns:

### Helper functions

Many common operations are provided as helper functions from src/Helpers.php.

### Result pattern

All client methods return `Success`/`Failure` objects instead of throwing exceptions:

```php
$result = $client->check(...);
$result->success(fn($response) => /* optional side effect to perform on success (such as logging, echoing, etc.) */)
       ->failure(fn($error) => /* optional side effect to perform on failure (such as logging, echoing, etc.) */)
       ->then(fn($result) => /* optional replacement of the result value on success */)
       ->recover(fn($error) => /* optional replacement of the result value on failure */)
       ->unwrap(fn($value) => /* optional transformation of the result value on success, or opportunity to fall back to a default value on failure without automatically throwing an exception */)
       ->unwrap(); // Get the value (on success) or throw an exception (on failure)
```

### Interface-first design

Every major class has a corresponding interface (for example, `Client` implements `ClientInterface`). Always code against interfaces when possible.

### Domain model structure

- **Models/** - Immutable domain objects with strict typing
- **Collections/** - Type-safe collections extending `IndexedCollection` or `KeyedCollection`
- **Requests/** - API request DTOs with validation
- **Responses/** - API response DTOs
- **Schema/** - JSON schema validation system for all models

### DSL transformer

The SDK includes a DSL parser (`Language/DslTransformer`) that converts human-readable authorization models to API objects:

```php
use function OpenFGA\dsl;

$model = dsl($dslString);
```

### PSR compliance

- Uses PSR-7 for HTTP messages
- PSR-17 for HTTP factories
- PSR-18 for HTTP clients
- Client accepts any PSR-compatible implementations

## Development guidelines

### Documentation philosophy

Our documentation follows these core principles:

#### Write for humans first

- Use a conversational yet professional tone - imagine explaining to a colleague
- Address readers directly as "you"
- Lead with the "why" before diving into the "how"
- Start each section with what problem it solves or why it matters
- Keep sentences short and punchy
- Avoid unnecessary jargon while maintaining technical accuracy

#### Structure for progressive learning

- Begin with a 1-2 sentence summary that captures the essence
- Progress from high-level concepts to implementation details
- Use section headers that form a natural reading flow
- Include anchor links (#section-name) for easy navigation
- End sections with clear next steps or related topics

#### Show, don't just tell

- Use realistic, contextual code examples from actual use cases
- Demonstrate related features together to show cohesion
- Keep examples concise but complete enough to be meaningful
- Avoid abstract foo/bar examples - use domain-relevant names
- Show both simple usage and advanced patterns when relevant

#### Embrace modern PHP

- Showcase PHP 8.3+ features naturally in examples
- Use modern syntax to educate by example
- Don't explain language features unless central to understanding

### Documentation standards

#### File and section organization

1. **README.md Structure:**

   - Start with a compelling one-paragraph summary of what the library does and why it exists
   - Include a "Quick Start" section with the simplest possible working example
   - Progress to "Core Concepts" explaining fundamental patterns
   - Add "Advanced Usage" for complex scenarios
   - End with "Next Steps" linking to deeper documentation

2. **Documentation Pages:**

   - One focused topic per page
   - Use descriptive filenames that match the topic
   - Follow a consistent structure: Overview → Concepts → Examples → Reference → Related Topics

3. **Code Examples in Documentation:**

   ```php
   // BAD: Abstract example
   $foo = new Thing();
   $foo->doSomething($bar);

   // GOOD: Contextual example showing real usage
   use OpenFGA\Client;

   use function OpenFGA\{dsl, model, store, tuple, write, allowed};

   $client = new Client(
       url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080'
   );

   $storeId = store(
       name: 'example-document-system',
       client: $client
   );

   echo "Created store: {$storeId}\n";

   $dsl = <<<DSL
   model
   schema 1.1

   type user

   type document
   relations
      define viewer: [user]
      define editor: [user]
   DSL;

   $model = dsl(
       dsl: $dsl,
       client: $client
   );

   $modelId = model(
       model: $model,
       store: $storeId,
       client: $client
   );

   echo "Created model: {$modelId}\n";

   write(
       client: $client,
       store: $storeId,
       model: $modelId,
       tuples: tuple('user:alice', 'viewer', 'document:readme')
   );

   echo "Granted alice viewer permission on readme\n";

   $canView = allowed(
       client: $client,
       store: $storeId,
       model: $modelId,
       tuple: tuple('user:alice', 'viewer', 'document:readme')
   );

   echo $canView ? "✅ Alice can view readme" : "❌ Access denied";
   ```

#### Interface and class documentation

- Every concrete class should have a corresponding interface (unless there's a specific reason not to)
- All public methods must be defined in their respective interfaces
- Concrete classes should use `@inheritDoc` for public methods implemented from interfaces
- Private methods in concrete classes need adequate docblocks

#### Public API documentation

Write docblocks that educate and guide:

```php
/**
 * Checks if a user has a specific relationship with an object.
 *
 * This method verifies whether the specified user has the given relationship
 * (like 'reader', 'writer', or 'owner') with the target object. It's the core
 * operation for making authorization decisions in your application.
 *
 * @param string $user The user identifier (for example 'user:anne')
 * @param string $relation The relationship to check (for example 'reader', 'writer')
 * @param string $object The object identifier (for example 'document:budget-2024')
 * @param array<string, mixed> $contextualTuples Optional tuples evaluated in this request only
 *
 * @return Result<CheckResponse> Success with allowed/not-allowed, or Failure with error details
 *
 * @see https://openfga.dev/docs/api#/Relationship%20Queries/Check
 */
```

Key requirements:

- Start with a one-sentence summary of what the method does
- Add a paragraph explaining when/why to use it
- Use meaningful parameter descriptions, not just type restatements
- Include example values in descriptions
- Reference external documentation with `@see` tags
- Focus on practical usage over implementation details

#### Writing style guidelines

1. **Be Direct and Clear:**

   - ❌ "This method can be utilized for the purpose of checking permissions"
   - ✅ "Check if a user has permission to access an object"

2. **Show Benefits First:**

   - ❌ "This implements the check endpoint from the OpenFGA API specification"
   - ✅ "Verify user permissions in your application with a single method call"

3. **Use Active Voice:**

   - ❌ "Exceptions will be thrown if validation fails"
   - ✅ "Throws ValidationException when the model syntax is invalid"

4. **Include Context in Examples:**

   ```php
   use function OpenFGA\{allowed, tuple};

   // Checking if a user can read a document
   $canRead = allowed(
       client: $client,
       store: $storeId,
       model: $modelId,
       tuple: tuple('user:anne', 'reader', 'document:budget-2024')
   );

   if ($canRead) {
       // User has read access, show the document
   }
   ```

### Import organization

Organize `use` statements in PHP files according to these rules:

1. Group imports in this order:

   - Constants (`use const`)
   - Internal classes (namespace starts with project root namespace)
   - External classes (vendor/third-party) with a namespace (like "Psr\Http\Message\ResponseInterface")
   - External classes (vendor/third-party) without a namespace (like "Override")
   - Functions (`use function`)

2. Sort alphabetically within each group

3. Combine imports from the same namespace using grouped syntax:

   ```php
   use Vendor\Package\{ClassA, ClassB, ClassC};
   ```

4. Preserve blank lines between groups

### Adding new features

1. Create interface first in the corresponding `*Interface.php` file
2. Write documentation that explains the feature's purpose before implementation
3. Implement the interface with full PHPDoc following the documentation philosophy
4. Add schema validation if it's a model class
5. Ensure our linters and existing tests pass
6. Write unit tests covering all paths using PEST v3 syntax
7. Update relevant documentation with real-world examples
8. Add the feature to appropriate guides/tutorials if significant

### Testing requirements

- Unit tests are required for all functionality
- Integration tests for all API endpoints
- Contract tests must pass against OpenAPI spec
- Minimum 90% code coverage
- Test names should describe behavior, not implementation

### Code standards

#### Philosophy

- Less is more, simplicity is key, essentialism is the goal
- Use the simplest solution that works and avoid over-engineering
- Always adhere to DRY and SOLID principles
- Always follow the law of demeter, separation of concerns, and the single responsibility principle
- Always design by contract
- Always use the most appropriate structure/pattern for the problem at hand
- Only use generics when there are no better options available, as they tend to introduce unnecessary complexity

#### PHP 8.3+ features

- Use PHP 8.3+ features where appropriate, including:
  - Typed class constants
  - Readonly properties
  - Constructor property promotion
  - Named arguments
  - Union types
  - Match expressions
  - Nullsafe operator
  - First-class callable syntax
  - New string functions
  - JSON validation exceptions
  - Dynamic class constant fetch
  - Deep-cloning of readonly properties
  - Random extension improvements

#### Type safety

- Use strict typing (`declare(strict_types=1)`)
- Leverage PHP 8.3's type system features
- Think hard about types and use them to your advantage
- Use `never` return type for functions that never return
- Use `mixed` type only when absolutely necessary
- Use `@var` annotations only when absolutely necessary
- Never use suppression techniques to suppress errors, fix the root cause
- When PHPStan and Psalm can't agree, look for a solution that satisfies both
- If a suppression has to be applied, apply it at the method or class level, never inline

#### Code organization

- Follow PSR-12 coding standards
- Keep lines under 120 characters
- Use property type declarations for all properties
- Use constructor property promotion where appropriate
- Make classes `final` by default unless designed for extension
- Use `readonly` for immutable properties
- Follow existing patterns for consistency

#### Error handling

- Use exceptions for exceptional cases, not for control flow
- Create specific exception classes for different error cases
- Include context in exceptions for better debugging
- Follow the Result pattern for expected error cases
- Write exception messages that help developers understand what went wrong and how to fix it

### Documentation review checklist

Before committing documentation:

- [ ] Does it start by explaining what problem this solves?
- [ ] Is the tone conversational yet professional?
- [ ] Are code examples realistic and from actual use cases?
- [ ] Does it progress logically from simple to complex?
- [ ] Are related features shown together?
- [ ] Does it link to relevant deeper content?
- [ ] Would a developer new to the library understand this?
- [ ] Does it feel lightweight while being comprehensive?
