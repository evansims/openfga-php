# SchemaBuilderInterface

Interface for building schema definitions using the builder pattern. This interface provides a fluent API for constructing schema definitions that describe the structure and validation rules for OpenFGA model objects. The builder pattern allows for easy, readable schema creation with method chaining. Schema builders support all common data types including strings, integers, booleans, dates, arrays, and complex objects. Each property can be configured with validation rules such as required status, default values, format constraints, and enumeration restrictions. Example usage: ```php $schema = $builder -&gt;string(&#039;name&#039;, required: true) -&gt;integer(&#039;age&#039;, required: false, default: 0) -&gt;object(&#039;address&#039;, Address::class, required: true) -&gt;register(); ``` The built schemas are automatically registered in the SchemaRegistry for use during validation and object transformation throughout the OpenFGA system.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`array()`](#array)
  - [`boolean()`](#boolean)
  - [`date()`](#date)
  - [`datetime()`](#datetime)
  - [`integer()`](#integer)
  - [`number()`](#number)
  - [`object()`](#object)
  - [`register()`](#register)
  - [`string()`](#string)

</details>

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php)

## Related Classes

- [SchemaBuilder](Schemas/SchemaBuilder.md) (implementation)

## Methods

### array

```php
public function array(
    string $name,
    array $items,
    bool $required = false,
    mixed $default = NULL,
): self

```

Add an array property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L46)

#### Parameters

| Name        | Type     | Description                           |
| ----------- | -------- | ------------------------------------- |
| `$name`     | `string` | The property name                     |
| `$items`    | `array`  |                                       |
| `$required` | `bool`   | Whether the property is required      |
| `$default`  | `mixed`  | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### boolean

```php
public function boolean(string $name, bool $required = false, mixed|null $default = NULL): self

```

Add a boolean property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L56)

#### Parameters

| Name        | Type                  | Description                           |
| ----------- | --------------------- | ------------------------------------- |
| `$name`     | `string`              | The property name                     |
| `$required` | `bool`                | Whether the property is required      |
| `$default`  | `mixed` &#124; `null` | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### date

```php
public function date(string $name, bool $required = false, mixed|null $default = NULL): self

```

Add a date property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L66)

#### Parameters

| Name        | Type                  | Description                           |
| ----------- | --------------------- | ------------------------------------- |
| `$name`     | `string`              | The property name                     |
| `$required` | `bool`                | Whether the property is required      |
| `$default`  | `mixed` &#124; `null` | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### datetime

```php
public function datetime(string $name, bool $required = false, mixed|null $default = NULL): self

```

Add a datetime property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L76)

#### Parameters

| Name        | Type                  | Description                           |
| ----------- | --------------------- | ------------------------------------- |
| `$name`     | `string`              | The property name                     |
| `$required` | `bool`                | Whether the property is required      |
| `$default`  | `mixed` &#124; `null` | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### integer

```php
public function integer(string $name, bool $required = false, mixed|null $default = NULL): self

```

Add an integer property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L86)

#### Parameters

| Name        | Type                  | Description                           |
| ----------- | --------------------- | ------------------------------------- |
| `$name`     | `string`              | The property name                     |
| `$required` | `bool`                | Whether the property is required      |
| `$default`  | `mixed` &#124; `null` | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### number

```php
public function number(string $name, bool $required = false, mixed|null $default = NULL): self

```

Add a number (float) property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L96)

#### Parameters

| Name        | Type                  | Description                           |
| ----------- | --------------------- | ------------------------------------- |
| `$name`     | `string`              | The property name                     |
| `$required` | `bool`                | Whether the property is required      |
| `$default`  | `mixed` &#124; `null` | Default value for optional properties |

#### Returns

`self` — Returns the builder instance for method chaining

### object

```php
public function object(string $name, string $className, bool $required = false): self

```

Add an object property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L106)

#### Parameters

| Name         | Type     | Description                            |
| ------------ | -------- | -------------------------------------- |
| `$name`      | `string` | The property name                      |
| `$className` | `string` | The class name for the object property |
| `$required`  | `bool`   | Whether the property is required       |

#### Returns

`self` — Returns the builder instance for method chaining

### register

```php
public function register(): Schema

```

Build and register the schema. Creates a Schema instance with all defined properties and registers it in the SchemaRegistry for use in validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L116)

#### Returns

[`Schema`](Schema.md) — The built and registered schema

### string

```php
public function string(
    string $name,
    bool $required = false,
    string|null $format = NULL,
    array<string>|null $enum = NULL,
    mixed $default = NULL,
): self

```

Add a string property to the schema.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaBuilderInterface.php#L128)

#### Parameters

| Name        | Type                                  | Description                                                                   |
| ----------- | ------------------------------------- | ----------------------------------------------------------------------------- |
| `$name`     | `string`                              | The property name                                                             |
| `$required` | `bool`                                | Whether the property is required                                              |
| `$format`   | `string` &#124; `null`                | String format constraint (for example &#039;date&#039;, &#039;datetime&#039;) |
| `$enum`     | `array&lt;`string`&gt;` &#124; `null` | Array of allowed string values                                                |
| `$default`  | `mixed`                               | Default value for optional properties                                         |

#### Returns

`self` — Returns the builder instance for method chaining
