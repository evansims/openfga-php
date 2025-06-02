# SchemaValidatorInterface

Interface for schema validation and object transformation in the OpenFGA system. This interface defines the contract for validating raw data (typically from JSON API responses) against registered schemas and transforming that data into properly typed OpenFGA model objects. The validator ensures data integrity and type safety throughout the SDK. Schema validators maintain a registry of schemas and provide validation services for both individual objects and collections. They handle complex validation scenarios including nested objects, arrays, conditional properties, and custom format constraints. The transformation process creates fully initialized model objects with proper type casting, default value handling, and constructor parameter mapping based on the schema definitions.

## Namespace
`OpenFGA\Schema`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaValidatorInterface.php)

## Related Classes
* [SchemaValidator](Schema/SchemaValidator.md) (implementation)

## Methods

### Authorization
#### validateAndTransform

```php
public function validateAndTransform(mixed $data, string $className): T
```

Validate data against a registered schema and transform it into the target class instance. This method performs comprehensive validation of the provided data against the schema for the specified class name. If validation succeeds, it creates and returns a fully initialized instance of the target class with all data properly transformed and typed. The validation process includes: - Required field validation - Type checking and conversion - Format validation (dates, enums, etc.) - Nested object validation - Collection validation for arrays - Constructor parameter mapping - Default value application

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaValidatorInterface.php#L78)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$data` | `mixed` | The raw data to validate (typically an array from JSON) |
| `$className` | `string` | The fully qualified class name to validate against |

#### Returns
`T` — The validated and transformed object instance
### List Operations
#### getSchemas

```php
public function getSchemas(): array<string, SchemaInterface>
```

Get all currently registered schemas. Returns a comprehensive map of all schemas that have been registered with this validator, keyed by their associated class names. This is useful for debugging, introspection, and understanding which schemas are available for validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaValidatorInterface.php#L39)

#### Returns
`array&lt;`string`, [`SchemaInterface`](SchemaInterface.md)&gt;` — Map of class names to their schema definitions
### Model Management
#### registerSchema

```php
public function registerSchema(SchemaInterface $schema): self
```

Register a schema for validation use. Adds a schema to the validator&#039;s registry, making it available for use in validation and transformation operations. Schemas must be registered before they can be used to validate data for their associated class.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaValidatorInterface.php#L51)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$schema` | [`SchemaInterface`](SchemaInterface.md) | The schema definition to register |

#### Returns
`self` — Returns the validator instance for method chaining
