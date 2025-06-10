# Schemas

[API Documentation](../README.md) > Schemas

JSON schema validation for ensuring data integrity and type safety.

**Total Components:** 14

## Interfaces

| Name | Description |
|------|-------------|
| [`CollectionSchemaInterface`](./CollectionSchemaInterface.md) | Interface for collection schema definitions in the OpenFGA system. This interface extends the bas... |
| [`SchemaBuilderInterface`](./SchemaBuilderInterface.md) | Interface for building schema definitions using the builder pattern. This interface provides a fl... |
| [`SchemaInterface`](./SchemaInterface.md) | Base interface for schema definitions in the OpenFGA system. This interface defines the fundament... |
| [`SchemaPropertyInterface`](./SchemaPropertyInterface.md) | Interface for schema property definitions. This interface defines the contract for schema propert... |
| [`SchemaRegistryInterface`](./SchemaRegistryInterface.md) | Registry for managing schema definitions in the OpenFGA system. This interface provides a central... |
| [`SchemaValidatorInterface`](./SchemaValidatorInterface.md) | Interface for schema validation and object transformation in the OpenFGA system. This interface d... |
| [`ValidationServiceInterface`](./ValidationServiceInterface.md) | Service for validating data against schemas. This service encapsulates the validation logic, sepa... |

## Classes

| Name | Description |
|------|-------------|
| [`CollectionSchema`](./CollectionSchema.md) | Schema definition specifically for validating and transforming collection data structures. This s... |
| [`Schema`](./Schema.md) | JSON schema definition for validating and transforming data structures. This schema defines valid... |
| [`SchemaBuilder`](./SchemaBuilder.md) | Fluent builder for creating JSON schemas for data validation and transformation. This builder pro... |
| [`SchemaProperty`](./SchemaProperty.md) | Represents a single property definition within a schema. This class defines the validation rules,... |
| [`SchemaRegistry`](./SchemaRegistry.md) | Centralized registry for managing schema definitions across the OpenFGA system. This registry pro... |
| [`SchemaValidator`](./SchemaValidator.md) | Validates and transforms data according to registered JSON schemas. This validator ensures that A... |
| [`ValidationService`](./ValidationService.md) | Service for validating data against schemas. This service encapsulates validation logic, separati... |

---

[← Back to API Documentation](../README.md)
