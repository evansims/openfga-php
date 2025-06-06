# ValidationService

Service for validating data against schemas. This service encapsulates validation logic, separating it from object construction concerns in SchemaValidator. It provides validation for both complete data structures and individual properties, with detailed error reporting.

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/ValidationService.php)

## Implements

* [`ValidationServiceInterface`](ValidationServiceInterface.md)

## Related Classes

* [ValidationServiceInterface](Schemas/ValidationServiceInterface.md) (interface)

## Methods

### Authorization

#### validate

```php
public function validate(mixed $data, string $className): array<string, mixed>

```

Validate data against a schema. Validates the provided data against the schema for the specified class. This method only validates structure and types, it does not construct objects.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/ValidationService.php#L66)

#### Parameters

| Name         | Type     | Description                                     |
| ------------ | -------- | ----------------------------------------------- |
| `$data`      | `mixed`  | The data to validate                            |
| `$className` | `string` | The class name whose schema to validate against |

#### Returns

`array&lt;`string`, `mixed`&gt;` — The validated data (may be normalized/cleaned)

#### validateProperty

```php
public function validateProperty(mixed $value, OpenFGA\Schemas\SchemaPropertyInterface $property, string $path): mixed

```

Validate a property value against its schema definition.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/ValidationService.php#L134)

#### Parameters

| Name        | Type                                                    | Description                           |
| ----------- | ------------------------------------------------------- | ------------------------------------- |
| `$value`    | `mixed`                                                 | The value to validate                 |
| `$property` | [`SchemaPropertyInterface`](SchemaPropertyInterface.md) | The property schema                   |
| `$path`     | `string`                                                | The property path for error reporting |

#### Returns

`mixed` — The validated value

### Model Management

#### hasSchema

```php
public function hasSchema(string $className): bool

```

Check if a schema is registered for a class.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/ValidationService.php#L41)

#### Parameters

| Name         | Type     | Description             |
| ------------ | -------- | ----------------------- |
| `$className` | `string` | The class name to check |

#### Returns

`bool` — True if schema is registered

#### registerSchema

```php
public function registerSchema(OpenFGA\Schemas\SchemaInterface $schema): self

```

Register a schema for validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/ValidationService.php#L50)

#### Parameters

| Name      | Type                                    | Description            |
| --------- | --------------------------------------- | ---------------------- |
| `$schema` | [`SchemaInterface`](SchemaInterface.md) | The schema to register |

#### Returns

`self` — For method chaining
