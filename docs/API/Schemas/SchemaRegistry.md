# SchemaRegistry

Centralized registry for managing schema definitions across the OpenFGA system. This registry provides a static, global repository for schema definitions that can be accessed throughout the application lifecycle. It serves as the primary mechanism for storing, retrieving, and creating schema definitions for OpenFGA model objects. The registry supports both programmatic schema creation through the builder pattern and direct schema registration for pre-defined schemas. This centralized approach ensures consistent schema validation across all model objects and eliminates the need for redundant schema definitions. Schemas registered here are used by the SchemaValidator for object validation and transformation during API response processing and data serialization operations.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`create()`](#create)
  - [`get()`](#get)
  - [`register()`](#register)

</details>

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaRegistry.php)

## Implements

- [`SchemaRegistryInterface`](SchemaRegistryInterface.md)

## Related Classes

- [SchemaRegistryInterface](Schemas/SchemaRegistryInterface.md) (interface)

## Methods

### create

*<small>Implements Schemas\SchemaRegistryInterface</small>*

```php
public function create(string $className): SchemaBuilder

```

Create a new schema builder for the specified class.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaRegistryInterface.php#L29)

#### Parameters

| Name         | Type     | Description                    |
| ------------ | -------- | ------------------------------ |
| `$className` | `string` | The fully qualified class name |

#### Returns

[`SchemaBuilder`](SchemaBuilder.md) — A new schema builder instance

### get

*<small>Implements Schemas\SchemaRegistryInterface</small>*

```php
public function get(string $className): ?OpenFGA\Schemas\Schema

```

Retrieve a registered schema by class name.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaRegistryInterface.php#L37)

#### Parameters

| Name         | Type     | Description                    |
| ------------ | -------- | ------------------------------ |
| `$className` | `string` | The fully qualified class name |

#### Returns

[`Schema`](Schema.md) &#124; `null`

### register

*<small>Implements Schemas\SchemaRegistryInterface</small>*

```php
public function register(Schema $schema): void

```

Register a schema in the registry.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaRegistryInterface.php#L44)

#### Parameters

| Name      | Type                  | Description                     |
| --------- | --------------------- | ------------------------------- |
| `$schema` | [`Schema`](Schema.md) | The schema instance to register |

#### Returns

`void`
