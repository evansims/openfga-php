---
title: OpenFGA PHP SDK
description: Official PHP SDK for OpenFGA - Fine-grained authorization made simple
---

# OpenFGA PHP SDK

The OpenFGA PHP SDK provides a convenient way to interact with [OpenFGA](https://openfga.dev), a high-performance and flexible authorization/permission engine built on Google Zanzibar.

The project is available on GitHub at [evansims/openfga-php](https://github.com/evansims/openfga-php).

## Quick Start

Get up and running with fine-grained authorization in your PHP application:

- 📚 **[Getting Started Guide](GettingStarted.md)** - Installation, setup, and your first authorization check
- 🔐 **[Authentication](Authentication.md)** - Configure API credentials and access tokens
- 🏪 **[Stores](Stores.md)** - Create and manage authorization stores
- 📝 **[Authorization Models](AuthorizationModels.md)** - Define your permission rules
- 🔍 **[Queries](Queries.md)** - Check permissions and expand relationships
- 📊 **[Relationship Tuples](RelationshipTuples.md)** - Manage user-resource relationships
- 🎯 **[Results](Results.md)** - Handle success and failure responses

## API Reference

Comprehensive documentation for all SDK classes and methods:

- **[Client](API/Client.md)** - Main SDK client interface
- **[Models](API/Models/)** - Data structures and domain objects
- **[Requests](API/Requests/)** - API request builders
- **[Responses](API/Responses/)** - API response objects
- **[Exceptions](API/Exceptions/)** - Error handling and exception types

## Features

- ✅ **PHP 8.3+** - Modern PHP with strict typing
- ✅ **Result Pattern** - Elegant error handling without exceptions
- ✅ **PSR Compliant** - Works with any PSR-7/17/18 HTTP implementation
- ✅ **Type Safe** - Full type hints and IDE support
- ✅ **DSL Support** - Human-readable authorization model syntax
- ✅ **Comprehensive** - Complete OpenFGA API coverage

## Installation

```bash
composer require evansims/openfga-php
```

## Quick Example

```php
use OpenFGA\SDK\Client;
use OpenFGA\SDK\ClientConfiguration;

// Configure the client
$config = new ClientConfiguration([
    'apiUrl' => 'https://api.fga.example',
    'storeId' => 'your-store-id',
    'authorizationModelId' => 'your-model-id'
]);

$client = new Client($config);

// Check if a user can read a document
$result = $client->check([
    'user' => 'user:alice',
    'relation' => 'reader',
    'object' => 'document:readme'
]);

$result->success(fn($response) => $response->getAllowed())
       ->failure(fn($error) => throw $error)
       ->unwrap(); // true or false
```

## Need Help?

- 📖 **[OpenFGA Documentation](https://openfga.dev/docs)** - Learn authorization concepts
- 🐛 **[Report Issues](https://github.com/evansims/openfga-php/issues)** - Found a bug or need a feature?
