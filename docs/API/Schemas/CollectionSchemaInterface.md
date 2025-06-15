# CollectionSchemaInterface

Interface for collection schema definitions in the OpenFGA system. This interface extends the base SchemaInterface to provide specialized validation and structure definitions for collections of objects. Collection schemas handle arrays and lists of objects that conform to specific types, with support for wrapper keys and item requirements. Collection schemas are essential for validating complex data structures like lists of users, authorization models, relationship tuples, and other grouped data returned by the OpenFGA API. Examples of collections include Users, AuthorizationModels, Tuples, and other array-based response data that require consistent validation and type safety.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getClassName()`](#getclassname)
  - [`getItemType()`](#getitemtype)
  - [`getProperties()`](#getproperties)
  - [`getProperty()`](#getproperty)
  - [`getWrapperKey()`](#getwrapperkey)
  - [`requiresItems()`](#requiresitems)

</details>

## Namespace

`OpenFGA\Schemas`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchemaInterface.php)

## Implements

- [`SchemaInterface`](SchemaInterface.md)

## Related Classes

- [SchemaInterface](Schemas/SchemaInterface.md) (item)
- [CollectionSchema](Schemas/CollectionSchema.md) (implementation)

## Methods

### getClassName

```php
public function getClassName(): string

```

Get the fully qualified class name this schema defines. This method returns the class name that this schema describes, which is used during validation and object instantiation to ensure the correct model class is created.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaInterface.php#L38)

#### Returns

`string` — The fully qualified class name for the model this schema defines

### getItemType

```php
public function getItemType(): string

```

Get the type of each item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchemaInterface.php#L38)

#### Returns

`string`

### getProperties

```php
public function getProperties(): array<string, SchemaProperty>

```

Get all properties defined in this schema. This method returns a comprehensive collection of all properties that make up this schema, including their validation rules, types, and default values. Each property defines how a specific field should be validated and processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaInterface.php#L49)

#### Returns

`array&lt;`string`, [`SchemaProperty`](SchemaProperty.md)&gt;` — An associative array of property names to their schema property definitions

### getProperty

```php
public function getProperty(string $name): SchemaProperty|null

```

Get a specific property definition by name. This method retrieves the schema definition for a particular property, allowing you to access its validation rules, type information, and other metadata for individual fields.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/SchemaInterface.php#L60)

#### Parameters

| Name    | Type     | Description                          |
| ------- | -------- | ------------------------------------ |
| `$name` | `string` | The name of the property to retrieve |

#### Returns

[`SchemaProperty`](SchemaProperty.md) &#124; `null` — The property definition if it exists, or null if the property is not defined in this schema

### getWrapperKey

```php
public function getWrapperKey(): string|null

```

Get the wrapper key for the collection data if any. Some collections expect data wrapped in a specific key (for example, Usersets uses &#039;child&#039;).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchemaInterface.php#L47)

#### Returns

`string` &#124; `null` — The wrapper key or null if data is not wrapped

### requiresItems

```php
public function requiresItems(): bool

```

Whether the collection requires at least one item.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schemas/CollectionSchemaInterface.php#L54)

#### Returns

`bool` — True if the collection must contain at least one item, false if empty collections are allowed
