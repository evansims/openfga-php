# Changelog

## [1.1.0] - 2025-05-30

### Added

- OpenTelemetry tracing support

### Fixed

- Fixes to schema validation to handle more edge cases

## [1.0.0] - 2025-05-29

> _Fine-grained auth flows,_
> _PHP types guard each request—_
> _Permissions granted._

### Added

- Complete OpenFGA API implementation with full type safety
- Result pattern for elegant error handling without exceptions
- PSR-7/17/18 HTTP compliance for maximum compatibility
- DSL transformer for human-readable authorization models
- Comprehensive schema validation with detailed error reporting
- Extensive test coverage (90%+) with integration and contract tests
- Rich documentation with GitHub Pages deployment
- PHP 8.3+ support with modern language features

### Features

- **Client SDK**: Full-featured client with all OpenFGA endpoints
- **Type Safety**: Strict typing throughout with interface-first design
- **Authentication**: Multiple auth methods including client credentials
- **Models**: Complete domain object model with collections
- **Validation**: JSON schema validation for all requests/responses
- **DSL**: Parse and generate authorization models from readable syntax
- **Results**: Success/Failure pattern inspired by Rust and functional languages
- **Testing**: Comprehensive test suite with contract validation
