<div align="center">
  <p><a href="https://tempestphp.com"><img src="https://raw.githubusercontent.com/evansims/openfga-php/main/.github/openfga.png" width="100" /></a></p>

  <h1>OpenFGA PHP SDK</h1>

  <p>Stop writing authorization logic. Start asking questions.</p>

  <p><code>composer require evansims/openfga-php</code></p>
</div>

<p><br /></p>

## Getting Started

Build your authorization integration

- [Introduction](Introduction.md)<br />
  Start here to get your first authorization check working.
- [Installation](Introduction.md#install-the-sdk)<br />
  Install the SDK and set up your first authorization store.
- [Authentication](Authentication.md)<br />
  Set up authentication for production environments and managed services.

## Essentials

Learn the core concepts and patterns

- [Stores](Stores.md)<br />
  Manage authorization stores for multi-tenant applications and environment separation.
- [Authorization Models](Models.md)<br />
  Learn how to define your permission rules using OpenFGA's intuitive DSL.
- [Relationship Tuples](Tuples.md)<br />
  Understand how to grant and revoke specific permissions between users and resources.
- [Permission Queries](Queries.md)<br />
  Master the four types of queries: check permissions, list objects, find users, and expand relationships.
- [Assertions](Assertions.md)<br />
  Define test cases to verify your authorization model.

## Features

Explore advanced features and patterns

- [Concurrency](Concurrency.md)<br />
  Leveraging the SDK's powerful concurrency features to improve performance when working with large-scale authorization operations.
- [Exceptions](Exceptions.md)<br />
  Handling errors and exceptions in your authorization system.
- [Integration](Integration.md)<br />
  Integrating OpenFGA with your existing systems and frameworks.
- [Observability](Observability.md)<br />
  Comprehensive tracing and metrics to monitor your authorization system.
- [Results](Results.md)<br />
  Building robust applications with proper response handling using the SDK's Result pattern.
