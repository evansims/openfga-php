# TypedWildcardInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* Stringable
* JsonSerializable



## Methods
### getType


```php
public function getType(): string
```

Get the object type that this wildcard represents. This returns the type name for which the wildcard grants access to all users of that type. For example, &quot;user&quot; would represent all users, &quot;group&quot; would represent all groups, etc.


#### Returns
string
 The object type that this wildcard represents

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

