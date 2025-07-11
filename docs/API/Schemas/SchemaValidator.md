# SchemaValidator

Validates and transforms data according to registered JSON schemas. This validator ensures that API response data conforms to expected schemas and transforms raw arrays into strongly typed model objects. It handles nested objects, collections, and complex validation rules while providing detailed error reporting for schema violations.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getSchemas()`](#getschemas)
  - [`registerSchema()`](#registerschema)
  - [`validateAndTransform()`](#validateandtransform)

</details>

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaValidator.php)

## Implements

- [`SchemaValidatorInterface`](SchemaValidatorInterface.md)

## Related Classes

- [SchemaValidatorInterface](Schemas/SchemaValidatorInterface.md) (interface)

## Methods

### getSchemas

```php
public function getSchemas(): array

```

Get all currently registered schemas. Returns a comprehensive map of all schemas that have been registered with this validator, keyed by their associated class names. This is useful for debugging, introspection, and understanding which schemas are available for validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaValidator.php#L72)

#### Returns

`array` — Map of class names to their schema definitions

### registerSchema

```php
public function registerSchema(OpenFGA\Schemas\SchemaInterface $schema): self

```

Register a schema for validation use. Adds a schema to the validator&#039;s registry, making it available for use in validation and transformation operations. Schemas must be registered before they can be used to validate data for their associated class.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaValidator.php#L81)

#### Parameters

| Name      | Type                                    | Description                       |
| --------- | --------------------------------------- | --------------------------------- |
| `$schema` | [`SchemaInterface`](SchemaInterface.md) | The schema definition to register |

#### Returns

`self` — Returns the validator instance for method chaining

### validateAndTransform

```php
public function validateAndTransform(mixed $data, string $className): object

```

Validate data against a registered schema and transform it into the target class instance. This method performs comprehensive validation of the provided data against the schema for the specified class name. If validation succeeds, it creates and returns a fully initialized instance of the target class with all data properly transformed and typed. The validation process includes: - Required field validation - Type checking and conversion - Format validation (dates, enums, etc.) - Nested object validation - Collection validation for arrays - Constructor parameter mapping - Default value application

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaValidator.php#L96)

#### Parameters

| Name         | Type     | Description                                             |
| ------------ | -------- | ------------------------------------------------------- |
| `$data`      | `mixed`  | The raw data to validate (typically an array from JSON) |
| `$className` | `string` | The fully qualified class name to validate against      |

#### Returns

`object` — The validated and transformed object instance
