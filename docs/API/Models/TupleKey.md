# TupleKey

Represents a relationship tuple key defining a connection between user, relation, and object. A TupleKey is the fundamental unit of authorization in OpenFGA, representing a specific relationship like &quot;user:anne is reader of document:budget.&quot; It consists of three parts: user (who), relation (what type of access), and object (what resource), optionally with conditions for attribute-based access. Use this when creating, querying, or managing specific relationships in your authorization system.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`getCondition()`](#getcondition)
  - [`getObject()`](#getobject)
  - [`getRelation()`](#getrelation)
  - [`getUser()`](#getuser)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php)

## Implements

- [`TupleKeyInterface`](TupleKeyInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [TupleKeyInterface](Models/TupleKeyInterface.md) (interface)
- [TupleKeys](Models/Collections/TupleKeys.md) (collection)

## Constants

| Name            | Value      | Description |
| --------------- | ---------- | ----------- |
| `OPENAPI_MODEL` | `TupleKey` |             |

## Methods

### getCondition

```php
public function getCondition(): ?OpenFGA\Models\ConditionInterface

```

Get the condition that constrains this relationship. Conditions enable dynamic authorization by allowing relationships to be conditional based on runtime context, such as time of day, resource attributes, or other factors. When a condition is present, the relationship is only valid when the condition evaluates to true.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php#L80)

#### Returns

[`ConditionInterface`](ConditionInterface.md) &#124; `null` — The condition that must be satisfied for this relationship to be valid, or null for an unconditional relationship

### getObject

```php
public function getObject(): string

```

Get the object in this relationship tuple. The object represents the resource or entity that the permission or relationship applies to. For example, in &quot;user:alice can view document:readme,&quot; the object would be &quot;document:readme.&quot; Objects are typically formatted as &quot;type:id&quot; where type describes the kind of resource.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php#L89)

#### Returns

`string` — The object identifier

### getRelation

```php
public function getRelation(): string

```

Get the relation that defines the type of relationship. The relation describes what kind of permission or relationship exists between the user and object. For example, common relations include &quot;owner,&quot; &quot;viewer,&quot; &quot;editor,&quot; &quot;can_read,&quot; &quot;can_write.&quot; Relations are defined in your authorization model and determine what actions are permitted.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php#L98)

#### Returns

`string` — The relation name defining the type of relationship

### getUser

```php
public function getUser(): string

```

Get the user (subject) in this relationship tuple. The user represents the entity that has the relationship to the object. This can be an individual user, a group, a role, or any other subject defined in your authorization model. For example, in &quot;user:alice can view document:readme,&quot; the user would be &quot;user:alice.&quot;

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php#L107)

#### Returns

`string` — The user identifier

### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the tuple key for JSON encoding. This method prepares the tuple key data for API requests or storage, ensuring all components (user, relation, object, and optional condition) are properly formatted according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleKey.php#L116)

#### Returns

`array` — The serialized tuple key data ready for JSON encoding

### schema

*<small>Implements Models\TupleKeyInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model
