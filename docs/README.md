# OpenFGA PHP SDK

Modern fine-grained authorization for PHP applications. Build permission systems that scale from simple role checks to complex multi-tenant authorization patterns.

## What is this?

OpenFGA lets you answer questions like "Can Alice edit this document?" or "Which projects can Bob view?" without scattering permission logic throughout your codebase. Define your authorization rules once, then query them anywhere.

This SDK provides a modern PHP interface to [OpenFGA](https://openfga.dev) - Google's Zanzibar-inspired authorization engine that powers services like YouTube, Google Drive, and GitHub.

## Quick start

```bash
composer require evansims/openfga-php
```

```php
use OpenFGA\Client;
use function OpenFGA\{allowed, tuple};

$client = new Client(url: 'https://api.fga.example');

// Check permissions - simple and clean
$canEdit = allowed(
    client: $client,
    store: 'store_123',
    model: 'model_456', 
    tuple: tuple('user:alice', 'editor', 'document:readme')
);

// Find accessible resources
$documents = $client->listObjects(
    store: 'store_123',
    model: 'model_456',
    user: 'user:alice',
    relation: 'viewer',
    type: 'document'
)->unwrap()->getObjects();
```

## Why choose this SDK?

**Type-safe by design.** Every method has complete type hints. Your IDE knows exactly what you're working with.

**Error handling that makes sense.** No more try-catch blocks everywhere. The Result pattern lets you handle success and failure elegantly.

**Modern PHP patterns.** Built for PHP 8.3+ with property promotion, named arguments, and strict typing throughout.

**Production ready.** Comprehensive OpenTelemetry support, retry logic, circuit breakers, and graceful error handling.

## Core concepts

### Getting started → Introduction

New to OpenFGA? Start here to understand the basics and get your first authorization check working.

**[Getting Started Guide →](Introduction.md)**

### Core concepts → Authorization models

Learn how to define your permission rules using OpenFGA's intuitive DSL.

**[Authorization Models →](Models.md)**

### Core concepts → Relationship tuples

Understand how to grant and revoke specific permissions between users and resources.

**[Relationship Tuples →](Tuples.md)**

### Querying → Permission checks

Master the four types of queries: check permissions, list objects, find users, and expand relationships.

**[Queries →](Queries.md)**

## Configuration

### Authentication → API credentials

Set up authentication for production environments and managed services.

**[Authentication →](Authentication.md)**

### Configuration → Stores

Manage authorization stores for multi-tenant applications and environment separation.

**[Stores →](Stores.md)**

### Configuration → Error handling

Build robust applications with proper error handling and the Result pattern.

**[Results →](Results.md)**

### Observability → OpenTelemetry

Add comprehensive tracing and metrics to monitor your authorization system.

**[Observability →](Observability.md)**

## Key features

**Modern PHP 8.3+** — Property promotion, named arguments, and strict typing
**Result pattern** — Elegant error handling without exceptions  
**PSR compliant** — Works with any PSR-7/17/18 HTTP implementation
**Type safe** — Complete type hints and IDE support
**DSL support** — Human-readable authorization model syntax
**Observability** — Built-in OpenTelemetry tracing and metrics
**Production ready** — Retry logic, circuit breakers, and graceful degradation

## Need help?

**[OpenFGA Documentation](https://openfga.dev/docs)** — Learn authorization concepts and patterns
**[Report Issues](https://github.com/evansims/openfga-php/issues)** — Found a bug or need a feature?
**[API Reference](API/)** — Complete method documentation and examples
