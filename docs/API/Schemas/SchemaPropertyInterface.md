# SchemaPropertyInterface

Interface for schema property definitions. This interface defines the contract for schema property objects that specify validation rules, type information, and metadata for individual properties of OpenFGA model objects. Each property defines how a field should be validated, transformed, and mapped during object creation. Properties support various data types including primitives (string, int, bool), complex objects, arrays, and collections, with optional validation constraints such as required status, default values, format restrictions, and enumeration limits.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getClassName()`](#getclassname)
  - [`getDefault()`](#getdefault)
  - [`getEnum()`](#getenum)
  - [`getFormat()`](#getformat)
  - [`getItems()`](#getitems)
  - [`getName()`](#getname)
  - [`getParameterName()`](#getparametername)
  - [`getType()`](#gettype)
  - [`isRequired()`](#isrequired)

</details>

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php)

## Related Classes

- [SchemaProperty](Schemas/SchemaProperty.md) (implementation)

## Methods

### getClassName

```php
public function getClassName(): ?string

```

Get the fully qualified class name for object types.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L29)

#### Returns

`string` &#124; `null`

### getDefault

```php
public function getDefault(): mixed

```

Get the default value to use when property is missing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L36)

#### Returns

`mixed` — Default value for optional properties

### getEnum

```php
public function getEnum(): array<string>|null

```

Get the array of allowed values for enumeration validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L43)

#### Returns

`array&lt;`string`&gt;` &#124; `null` — Array of allowed values or null if not an enumeration

### getFormat

```php
public function getFormat(): string|null

```

Get the additional format constraint for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L50)

#### Returns

`string` &#124; `null` — Format constraint (for example &#039;date&#039;, &#039;datetime&#039;) or null if none

### getItems

```php
public function getItems(): ?array

```

Get the type specification for array items.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L57)

#### Returns

`array` &#124; `null`

### getName

```php
public function getName(): string

```

Get the property name as it appears in the data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L64)

#### Returns

`string` — The property name

### getParameterName

```php
public function getParameterName(): string|null

```

Get the alternative parameter name for constructor mapping.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L71)

#### Returns

`string` &#124; `null` — Alternative parameter name or null if using default mapping

### getType

```php
public function getType(): string

```

Get the data type for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L78)

#### Returns

`string` — The data type (string, integer, boolean, array, object, etc.)

### isRequired

```php
public function isRequired(): bool

```

Check if this property is required for validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaPropertyInterface.php#L85)

#### Returns

`bool` — True if the property is required, false otherwise
