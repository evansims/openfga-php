# CollectionSchema

Schema definition specifically for validating and transforming collection data structures. This specialized schema handles arrays of objects, providing validation rules for collections while ensuring each item conforms to the specified item type. It supports wrapper keys for nested collection data and optional item requirements.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getClassName()`](#getclassname)
  - [`getItemType()`](#getitemtype)
  - [`getProperties()`](#getproperties)
  - [`getProperty()`](#getproperty)
  - [`getWrapperKey()`](#getwrapperkey)
- [Other](#other)
  - [`requiresItems()`](#requiresitems)

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php)

## Implements

- [`CollectionSchemaInterface`](CollectionSchemaInterface.md)
- [`SchemaInterface`](SchemaInterface.md)

## Related Classes

- [CollectionSchemaInterface](Schemas/CollectionSchemaInterface.md) (interface)
- [Schema](Schemas/Schema.md) (item)

## Methods

### List Operations

#### getClassName

```php
public function getClassName(): string

```

Get the fully qualified class name this schema defines. This method returns the class name that this schema describes, which is used during validation and object instantiation to ensure the correct model class is created.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L56)

#### Returns

`string` — The fully qualified class name for the model this schema defines

#### getItemType

```php
public function getItemType(): string

```

Get the type of each item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L65)

#### Returns

`string`

#### getProperties

```php
public function getProperties(): array

```

Get all properties defined in this schema. This method returns a comprehensive collection of all properties that make up this schema, including their validation rules, types, and default values. Each property defines how a specific field should be validated and processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L74)

#### Returns

`array` — An associative array of property names to their schema property definitions

#### getProperty

```php
public function getProperty(string $name): ?OpenFGA\Schemas\SchemaProperty

```

Get a specific property definition by name. This method retrieves the schema definition for a particular property, allowing you to access its validation rules, type information, and other metadata for individual fields.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L84)

#### Parameters

| Name    | Type     | Description                          |
| ------- | -------- | ------------------------------------ |
| `$name` | `string` | The name of the property to retrieve |

#### Returns

[`SchemaProperty`](SchemaProperty.md) &#124; `null` — The property definition if it exists, or null if the property is not defined in this schema

#### getWrapperKey

```php
public function getWrapperKey(): ?string

```

Get the wrapper key for the collection data if any. Some collections expect data wrapped in a specific key (for example, Usersets uses &#039;child&#039;).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L94)

#### Returns

`string` &#124; `null` — The wrapper key or null if data is not wrapped

### Other

#### requiresItems

```php
public function requiresItems(): bool

```

Whether the collection requires at least one item.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchema.php#L103)

#### Returns

`bool` — True if the collection must contain at least one item, false if empty collections are allowed
