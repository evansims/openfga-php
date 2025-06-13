# UserObject

Represents a specific user object with type and identifier. A UserObject provides a structured way to represent users in your authorization system with both a type (like &quot;user,&quot; &quot;service,&quot; &quot;bot&quot;) and a unique identifier. This allows for clear categorization of different kinds of entities that can have permissions in your system. Use this when you need to represent users in a structured format rather than simple string identifiers.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Constants](#constants)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getId()`](#getid)
    * [`getType()`](#gettype)
* [Model Management](#model-management)
    * [`schema()`](#schema)
* [Other](#other)
    * [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObject.php)

## Implements

* [`UserObjectInterface`](UserObjectInterface.md)
* `Stringable`
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [UserObjectInterface](Models/UserObjectInterface.md) (interface)

## Constants

| Name            | Value        | Description |
| --------------- | ------------ | ----------- |
| `OPENAPI_MODEL` | `UserObject` |             |

## Methods

### List Operations

#### getId

```php
public function getId(): string

```

Get the unique identifier of the user object. The ID is unique within the context of the object type and represents the specific instance of the typed object.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObject.php#L69)

#### Returns

`string` — The object identifier

#### getType

```php
public function getType(): string

```

Get the type of the user object. The type defines the category or class of the object (for example &#039;user&#039;, &#039;group&#039;, &#039;organization&#039;) and must be defined in the authorization model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObject.php#L78)

#### Returns

`string` — The object type

### Model Management

#### schema

*<small>Implements Models\UserObjectInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the user object to its JSON representation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserObject.php#L87)

#### Returns

`array`
