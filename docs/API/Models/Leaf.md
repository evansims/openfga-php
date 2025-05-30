# Leaf


## Namespace
`OpenFGA\Models`

## Implements
* [LeafInterface](Models/LeafInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Leaf&#039;` |  |


## Methods
### getComputed


```php
public function getComputed(): ?OpenFGA\Models\ComputedInterface
```

Get the computed userset specification for this leaf. When present, this defines a computed relationship that resolves to other usersets dynamically. This allows for indirect relationships where users are determined by following other relations.


#### Returns
?[ComputedInterface](Models/ComputedInterface.md)
 The computed userset specification, or null if not used

### getTupleToUserset


```php
public function getTupleToUserset(): ?OpenFGA\Models\UsersetTreeTupleToUsersetInterface
```

Get the tuple-to-userset operation for this leaf. When present, this defines how to compute users by examining tuples and resolving them to usersets. This enables complex relationship patterns where users are derived from tuple relationships.


#### Returns
?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)
 The tuple-to-userset operation, or null if not used

### getUsers


```php
public function getUsers(): ?OpenFGA\Models\Collections\UsersListInterface
```

Get the direct list of users for this leaf node. When present, this provides an explicit list of users who have access through this leaf. This is used for direct user assignments rather than computed or derived access patterns.


#### Returns
?[UsersListInterface](Models/Collections/UsersListInterface.md)
 The list of users with direct access, or null if not used

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\LeafInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

