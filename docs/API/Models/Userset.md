# Userset

Represents a userset specification for computing groups of users. A Userset defines how to compute a collection of users through various means: computed relationships, tuple-to-userset operations, unions, intersections, or differences. This is a fundamental building block for complex authorization patterns where user groups are derived dynamically. Use this when defining how groups of users should be computed in your authorization model rules.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getComputedUserset()`](#getcomputeduserset)
  - [`getDifference()`](#getdifference)
  - [`getDirect()`](#getdirect)
  - [`getIntersection()`](#getintersection)
  - [`getTupleToUserset()`](#gettupletouserset)
  - [`getUnion()`](#getunion)
- [Model Management](#model-management)
  - [`schema()`](#schema)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php)

## Implements

- [`UsersetInterface`](UsersetInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [UsersetInterface](Models/UsersetInterface.md) (interface)

## Constants

| Name            | Value     | Description |
| --------------- | --------- | ----------- |
| `OPENAPI_MODEL` | `Userset` |             |

## Methods

### List Operations

#### getComputedUserset

```php
public function getComputedUserset(): ?OpenFGA\Models\ObjectRelationInterface

```

Get the computed userset specification for this userset. A computed userset defines relationships that are derived from other relationships, allowing for indirect authorization patterns. When present, this specifies an object-relation pair that should be computed to determine the actual users.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L70)

#### Returns

[`ObjectRelationInterface`](ObjectRelationInterface.md) &#124; `null` — The computed userset specification, or null if not used

#### getDifference

```php
public function getDifference(): ?OpenFGA\Models\DifferenceV1Interface

```

Get the difference operation specification for this userset. A difference operation represents a set subtraction where users are granted access based on one userset but explicitly excluded if they&#039;re in another. This enables sophisticated access control patterns like &quot;all managers except those on leave.&quot;

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L79)

#### Returns

[`DifferenceV1Interface`](DifferenceV1Interface.md) &#124; `null` — The difference operation specification, or null if not used

#### getDirect

```php
public function getDirect(): ?object

```

Get the direct userset value for this userset. A direct userset represents an immediate, explicit relationship without complex computation. This is typically used for simple membership patterns where users are directly assigned to a role or permission.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L88)

#### Returns

`object` &#124; `null` — The direct userset value, or null if not used

#### getIntersection

```php
public function getIntersection(): ?OpenFGA\Models\Collections\UsersetsInterface

```

Get the intersection operation specification for this userset. An intersection operation represents users who must satisfy ALL of the specified usersets. This creates a logical AND operation where users are granted access only if they&#039;re in every userset within the intersection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L97)

#### Returns

[`UsersetsInterface`](Models/Collections/UsersetsInterface.md) &#124; `null` — The collection of usersets to intersect, or null if not used

#### getTupleToUserset

```php
public function getTupleToUserset(): ?OpenFGA\Models\TupleToUsersetV1Interface

```

Get the tuple-to-userset operation specification for this userset. A tuple-to-userset operation computes users by examining existing relationships and following them to other usersets. This enables complex authorization patterns where permissions are inherited through relationship chains.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L106)

#### Returns

[`TupleToUsersetV1Interface`](TupleToUsersetV1Interface.md) &#124; `null` — The tuple-to-userset operation specification, or null if not used

#### getUnion

```php
public function getUnion(): ?OpenFGA\Models\Collections\UsersetsInterface

```

Get the union operation specification for this userset. A union operation represents users who satisfy ANY of the specified usersets. This creates a logical OR operation where users are granted access if they&#039;re in at least one userset within the union.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L115)

#### Returns

[`UsersetsInterface`](Models/Collections/UsersetsInterface.md) &#124; `null` — The collection of usersets to unite, or null if not used

### Model Management

#### schema

*<small>Implements Models\UsersetInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Userset.php#L124)

#### Returns

`array`
