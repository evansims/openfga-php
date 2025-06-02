# UsersetInterface

Defines the contract for userset specifications in authorization models. A userset represents a collection of users that can be computed through various means: direct assignment, computed relationships, unions, intersections, or complex tuple-to-userset operations. This interface provides the foundation for all userset types used in OpenFGA authorization models. Use this when defining how groups of users are identified and computed in your authorization system.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)

* `JsonSerializable`

## Related Classes

* [Userset](Models/Userset.md) (implementation)

## Methods

### List Operations

#### getComputedUserset

```php
public function getComputedUserset(): ObjectRelationInterface|null

```

Get the computed userset specification for this userset. A computed userset defines relationships that are derived from other relationships, allowing for indirect authorization patterns. When present, this specifies an object-relation pair that should be computed to determine the actual users.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L32)

#### Returns

[`ObjectRelationInterface`](ObjectRelationInterface.md) &#124; `null` — The computed userset specification, or null if not used

#### getDifference

```php
public function getDifference(): DifferenceV1Interface|null

```

Get the difference operation specification for this userset. A difference operation represents a set subtraction where users are granted access based on one userset but explicitly excluded if they&#039;re in another. This enables sophisticated access control patterns like &quot;all managers except those on leave&quot;.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L43)

#### Returns

[`DifferenceV1Interface`](DifferenceV1Interface.md) &#124; `null` — The difference operation specification, or null if not used

#### getDirect

```php
public function getDirect(): object|null

```

Get the direct userset value for this userset. A direct userset represents an immediate, explicit relationship without complex computation. This is typically used for simple membership patterns where users are directly assigned to a role or permission.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L54)

#### Returns

`object` &#124; `null` — The direct userset value, or null if not used

#### getIntersection

```php
public function getIntersection(): UsersetsInterface<UsersetInterface>|null

```

Get the intersection operation specification for this userset. An intersection operation represents users who must satisfy ALL of the specified usersets. This creates a logical AND operation where users are granted access only if they&#039;re in every userset within the intersection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L65)

#### Returns

[`UsersetsInterface`](Models/Collections/UsersetsInterface.md)&lt;[`UsersetInterface`](UsersetInterface.md)&gt; &#124; `null` — The collection of usersets to intersect, or null if not used

#### getTupleToUserset

```php
public function getTupleToUserset(): TupleToUsersetV1Interface|null

```

Get the tuple-to-userset operation specification for this userset. A tuple-to-userset operation computes users by examining existing relationships and following them to other usersets. This enables complex authorization patterns where permissions are inherited through relationship chains.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L76)

#### Returns

[`TupleToUsersetV1Interface`](TupleToUsersetV1Interface.md) &#124; `null` — The tuple-to-userset operation specification, or null if not used

#### getUnion

```php
public function getUnion(): UsersetsInterface<UsersetInterface>|null

```

Get the union operation specification for this userset. A union operation represents users who satisfy ANY of the specified usersets. This creates a logical OR operation where users are granted access if they&#039;re in at least one userset within the union.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L87)

#### Returns

[`UsersetsInterface`](Models/Collections/UsersetsInterface.md)&lt;[`UsersetInterface`](UsersetInterface.md)&gt; &#124; `null` — The collection of usersets to unite, or null if not used

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetInterface.php#L100)

#### Returns

`array`
