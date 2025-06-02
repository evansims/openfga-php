# TypedWildcardInterface

Defines the contract for typed wildcard specifications. A typed wildcard represents &quot;all users of a specific type&quot; in authorization rules. Instead of listing individual users, you can grant permissions to all members of a user type (like &quot;all employees&quot; or &quot;all customers&quot;). Use this when you want to create broad permission grants that automatically apply to all users of a particular type without explicit enumeration.

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

