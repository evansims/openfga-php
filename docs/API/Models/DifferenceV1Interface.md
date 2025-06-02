# DifferenceV1Interface

Defines a difference operation between two usersets in authorization models. DifferenceV1 represents a set operation that computes &quot;base minus subtract&quot;, effectively granting access to users in the base userset while explicitly denying access to users in the subtract userset. This enables complex authorization patterns like &quot;all employees except contractors&quot; or &quot;organization members except suspended users&quot;. Use this interface when implementing authorization logic that requires explicit exclusion of certain users from a broader permission set.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1Interface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [DifferenceV1](Models/DifferenceV1.md) (implementation)



## Methods

                                                            
### List Operations
#### getBase


```php
public function getBase(): UsersetInterface
```

Get the base userset from which users will be subtracted. This represents the initial set of users or relationships from which the subtract userset will be removed to compute the final difference.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1Interface.php#L31)


#### Returns
[UsersetInterface](UsersetInterface.md)
 The base userset for the difference operation

#### getSubtract


```php
public function getSubtract(): UsersetInterface
```

Get the userset of users to subtract from the base userset. This represents the set of users or relationships that should be removed from the base userset to compute the final result of the difference operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1Interface.php#L41)


#### Returns
[UsersetInterface](UsersetInterface.md)
 The userset to subtract from the base

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1Interface.php#L61)


#### Returns
array

