# Examples

This directory contains example code for the OpenFGA PHP SDK.

## Getting Started Examples

- [Hello World](hello-world/example.php) - The simplest possible introduction to OpenFGA in just a few lines of code.
- [Quick Start](quick-start/example.php) - A comprehensive example demonstrating the essential steps to get started with OpenFGA.

## Advanced Examples

- [Duplicate Filtering](duplicate-filtering/example.php) - Demonstrates automatic duplicate tuple filtering and delete precedence.
- [Non-Transactional Writes](non-transactional-writes/example.php) - Shows Fiber-based parallel batch processing for high-performance operations.
- [Event-Driven Telemetry](event-driven-telemetry/example.php) - Custom event listeners for observability without tight coupling.
- [Observability](observability/example.php) - OpenTelemetry integration for production monitoring and tracing.

## Running Examples

To run the examples, you will need to have a running OpenFGA instance. See [docs/Introduction](../docs/Introduction.md) for instructions on how to get started.

Install the Composer dependencies from the SDK's root directory:

```bash
composer install
```

Run the examples using the following commands:

```bash
php hello-world/example.php
php quick-start/example.php
php duplicate-filtering/example.php
php non-transactional-writes/example.php
php event-driven-telemetry/example.php
php observability/example.php
```
