# SchemaBuilder

Fluent builder for creating JSON schemas for data validation and transformation. This builder provides a fluent API for defining validation schemas for model classes, supporting various data types, formats, and validation constraints. It&#039;s used internally by the SDK to validate API responses and ensure data integrity.

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php)

## Implements

* [`SchemaBuilderInterface`](SchemaBuilderInterface.md)

## Related Classes

* [SchemaBuilderInterface](Schemas/SchemaBuilderInterface.md) (interface)

## Methods

### Utility

#### register

```php
public function register(): OpenFGA\Schemas\Schema

```

Build and register the schema. Creates a Schema instance with all defined properties and registers it in the SchemaRegistry for use in validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L154)

#### Returns

[`Schema`](Schema.md) — The built and registered schema

### Other

#### array

```php
public function array(
    string $name,
    array $items,
    bool $required = false,
    mixed $default = NULL,
): self

```

Add an array property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L39)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$items`    | `array`  |                                       |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### boolean

```php
public function boolean(string $name, bool $required = false, mixed $default = NULL): self

```

Add a boolean property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L56)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### date

```php
public function date(string $name, bool $required = false, mixed $default = NULL): self

```

Add a date property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L72)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### datetime

```php
public function datetime(string $name, bool $required = false, mixed $default = NULL): self

```

Add a datetime property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L89)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### integer

```php
public function integer(string $name, bool $required = false, mixed $default = NULL): self

```

Add an integer property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L106)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### number

```php
public function number(string $name, bool $required = false, mixed $default = NULL): self

```

Add a number (float) property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L122)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

#### object

```php
public function object(string $name, string $className, bool $required = false): self

```

Add an object property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L138)

#### Parameters

| Name         | Type     | Description                            |
| ------------ | -------- | -------------------------------------- |
| `$name`      | `string` | The property name                      |
| `$className` | `string` | The class name for the object property |
| `$required`  | `bool`   | Whether the property is required       |

#### Returns

`self` — Returns the builder instance for method chaining

#### string

```php
public function string(
    string $name,
    bool $required = false,
    ?string $format = NULL,
    ?array $enum = NULL,
    mixed $default = NULL,
): self

```

Add a string property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilder.php#L166)

#### Parameters

| Name        | Type                   | Description                                                             |
| ----------- | ---------------------- | ----------------------------------------------------------------------- |
| `$name`     | `string`               | The property name                                                       |
| `$required` | `bool`                 | Whether the property is required                                        |
| `$format`   | `string` &#124; `null` | String format constraint (e.g., &#039;date&#039;, &#039;datetime&#039;) |
| `$enum`     | `array` &#124; `null`  | Array of allowed string values                                          |
| `$default`  | `mixed`                | Default value for optional properties                                   |

#### Returns

`self` — Returns the builder instance for method chaining
