# UsersetInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputedUserset


```php
public function getComputedUserset(): ObjectRelationInterface|null
```

Get the computed userset specification for this userset. A computed userset defines relationships that are derived from other relationships, allowing for indirect authorization patterns. When present, this specifies an object-relation pair that should be computed to determine the actual users.


#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md) | null
 The computed userset specification, or null if not used

### getDifference


```php
public function getDifference(): DifferenceV1Interface|null
```

Get the difference operation specification for this userset. A difference operation represents a set subtraction where users are granted access based on one userset but explicitly excluded if they&#039;re in another. This enables sophisticated access control patterns like &quot;all managers except those on leave&quot;.


#### Returns
[DifferenceV1Interface](Models/DifferenceV1Interface.md) | null
 The difference operation specification, or null if not used

### getDirect


```php
public function getDirect(): object|null
```

Get the direct userset value for this userset. A direct userset represents an immediate, explicit relationship without complex computation. This is typically used for simple membership patterns where users are directly assigned to a role or permission.


#### Returns
object | null
 The direct userset value, or null if not used

### getIntersection


```php
public function getIntersection(): UsersetsInterface<UsersetInterface>|null
```

Get the intersection operation specification for this userset. An intersection operation represents users who must satisfy ALL of the specified usersets. This creates a logical AND operation where users are granted access only if they&#039;re in every userset within the intersection.


#### Returns
UsersetsInterface&lt;[UsersetInterface](Models/UsersetInterface.md)&gt; | null
 The collection of usersets to intersect, or null if not used

### getTupleToUserset


```php
public function getTupleToUserset(): TupleToUsersetV1Interface|null
```

Get the tuple-to-userset operation specification for this userset. A tuple-to-userset operation computes users by examining existing relationships and following them to other usersets. This enables complex authorization patterns where permissions are inherited through relationship chains.


#### Returns
[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md) | null
 The tuple-to-userset operation specification, or null if not used

### getUnion


```php
public function getUnion(): UsersetsInterface<UsersetInterface>|null
```

Get the union operation specification for this userset. A union operation represents users who satisfy ANY of the specified usersets. This creates a logical OR operation where users are granted access if they&#039;re in at least one userset within the union.


#### Returns
UsersetsInterface&lt;[UsersetInterface](Models/UsersetInterface.md)&gt; | null
 The collection of usersets to unite, or null if not used

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

