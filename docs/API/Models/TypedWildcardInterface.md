# TypedWildcardInterface

Defines the contract for typed wildcard specifications. A typed wildcard represents &quot;all users of a specific type&quot; in authorization rules. Instead of listing individual users, you can grant permissions to all members of a user type (like &quot;all employees&quot; or &quot;all customers&quot;). Use this when you want to create broad permission grants that automatically apply to all users of a particular type without explicit enumeration.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getType()`](#gettype)
* [Other](#other)
    * [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcardInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `Stringable`
* `JsonSerializable`

## Related Classes

* [TypedWildcard](Models/TypedWildcard.md) (implementation)

## Methods

### List Operations

#### getType

```php
public function getType(): string

```

Get the object type that this wildcard represents. This returns the type name for which the wildcard grants access to all users of that type. For example, &quot;user&quot; would represent all users, &quot;group&quot; would represent all groups, etc.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcardInterface.php#L41)

#### Returns

`string` — The object type that this wildcard represents

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcardInterface.php#L47)

#### Returns

`array`
