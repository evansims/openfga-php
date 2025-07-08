<div align="center">
  <p><a href="https://openfga.dev"><img src="https://raw.githubusercontent.com/evansims/openfga-php/main/.github/openfga.png" width="100" /></a></p>

  <h1>OpenFGA PHP SDK</h1>

  <p>Stop writing authorization logic. Start asking questions.</p>

  <p><code>composer require evansims/openfga-php</code></p>
</div>

<p><br /></p>

## Getting Started

Build your authorization integration

- [Introduction](Getting Started/Introduction.md)<br />
  Start here to get your first authorization check working.
- [Installation](Getting Started/Installation.md)<br />
  Install the SDK and set up required dependencies for your environment.
- [Authentication](Getting Started/Authentication.md)<br />
  Set up authentication for production environments and managed services.

## Essentials

Learn the core concepts and patterns

- [Stores](Essentials/Stores.md)<br />
  Manage authorization stores for multi-tenant applications and environment separation.
- [Authorization Models](Essentials/Models.md)<br />
  Learn how to define your permission rules using OpenFGA's intuitive DSL.
- [Relationship Tuples](Essentials/Tuples.md)<br />
  Understand how to grant and revoke specific permissions between users and resources.
- [Permission Queries](Essentials/Queries.md)<br />
  Master the four types of queries: check permissions, list objects, find users, and expand relationships.
- [Assertions](Essentials/Assertions.md)<br />
  Define test cases to verify your authorization model.

## Features

Explore advanced features and patterns

- [Helper Functions](Features/Helpers.md)<br />
  Simplify your code with convenient helper functions for common authorization operations.
- [Concurrency](Features/Concurrency.md)<br />
  Leveraging the SDK's powerful concurrency features to improve performance when working with large-scale authorization operations.
- [Exceptions](Features/Exceptions.md)<br />
  Handling errors and exceptions in your authorization system.
- [Integration](Features/Integration.md)<br />
  Integrating OpenFGA with your existing systems and frameworks.
- [Observability](Features/Observability.md)<br />
  Essential tracing and metrics to monitor your authorization system, including advanced event-driven telemetry and custom monitoring patterns.
- [Results](Features/Results.md)<br />
  Building robust applications with proper response handling using the SDK's Result pattern.
