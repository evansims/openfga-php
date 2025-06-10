# Examples

This directory contains example code for the OpenFGA PHP SDK.

- [Quick Start](quick-start/example.php) - A simple example demonstrating the essential steps to get started with OpenFGA.
- [Observability](observability/example.php) - An example demonstrating how to use OpenTelemetry observability with the OpenFGA PHP SDK.
- [Explorer](explorer/) - A full-featured web application for managing OpenFGA authorization models, relationship tuples, and performing authorization checks.

## Running Examples

To run the examples, you will need to have a running OpenFGA instance. See [docs/Introduction](../docs/Introduction.md) for instructions on how to get started.

Install the Composer dependencies from the SDK's root directory:

```bash
composer install
```

Run the examples using the following commands:

```bash
php quick-start/example.php
php observability/example.php
```

For the Explorer web application:

```bash
cd explorer
php -S localhost:8080 -t public
```

Then open http://localhost:8080 in your web browser.
