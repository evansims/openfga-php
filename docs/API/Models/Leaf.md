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
public function getComputed(): ?[ComputedInterface](Models/ComputedInterface.md)
```



#### Returns
`?[ComputedInterface](Models/ComputedInterface.md)`

### getTupleToUserset


```php
public function getTupleToUserset(): ?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)
```



#### Returns
`?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)`

### getUsers


```php
public function getUsers(): ?[UsersListInterface](Models/Collections/UsersListInterface.md)
```



#### Returns
`?[UsersListInterface](Models/Collections/UsersListInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}

### schema

*<small>Implements Models\LeafInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

