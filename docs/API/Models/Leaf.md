# Leaf

Represents a leaf node in authorization evaluation trees containing specific users. A Leaf is a terminal node in the authorization evaluation tree that contains a concrete set of users rather than further computation rules. It represents the final resolved users at the end of an authorization evaluation path. Use this when you need to represent the actual users that result from authorization rule evaluation, as opposed to computed or derived usersets.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Leaf.php)

## Implements

* [`LeafInterface`](LeafInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [LeafInterface](Models/LeafInterface.md) (interface)

## Constants

| Name            | Value    | Description |
| --------------- | -------- | ----------- |
| `OPENAPI_MODEL` | `'Leaf'` |             |

## Methods

### List Operations

#### getComputed

```php
public function getComputed(): ?OpenFGA\Models\ComputedInterface

```

Get the computed userset specification for this leaf. When present, this defines a computed relationship that resolves to other usersets dynamically. This allows for indirect relationships where users are determined by following other relations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Leaf.php#L71)

#### Returns

[`ComputedInterface`](ComputedInterface.md) &#124; `null` — The computed userset specification, or null if not used

#### getTupleToUserset

```php
public function getTupleToUserset(): ?OpenFGA\Models\UsersetTreeTupleToUsersetInterface

```

Get the tuple-to-userset operation for this leaf. When present, this defines how to compute users by examining tuples and resolving them to usersets. This enables complex relationship patterns where users are derived from tuple relationships.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Leaf.php#L80)

#### Returns

[`UsersetTreeTupleToUsersetInterface`](UsersetTreeTupleToUsersetInterface.md) &#124; `null` — The tuple-to-userset operation, or null if not used

#### getUsers

```php
public function getUsers(): ?OpenFGA\Models\Collections\UsersListInterface

```

Get the direct list of users for this leaf node. When present, this provides an explicit list of users who have access through this leaf. This is used for direct user assignments rather than computed or derived access patterns.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Leaf.php#L89)

#### Returns

[`UsersListInterface`](Models/Collections/UsersListInterface.md) &#124; `null` — The list of users with direct access, or null if not used

### Model Management

#### schema

*<small>Implements Models\LeafInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Leaf.php#L98)

#### Returns

`array`
