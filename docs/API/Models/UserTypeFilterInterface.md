# UserTypeFilterInterface

Represents a filter for limiting users by their relationships to specific object types. User type filters are used in queries to narrow down the set of users based on their relationships to objects of particular types. This is particularly useful when you want to find users who have specific permissions or roles related to certain categories of resources. The filter can specify: - A required object type that users must be related to - An optional relation that further constrains the relationship type Examples: - Find all users related to &quot;document&quot; objects - Find all users who are &quot;viewers&quot; of &quot;folder&quot; objects - Find all users who are &quot;members&quot; of &quot;organization&quot; objects

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getRelation()`](#getrelation)
  - [`getType()`](#gettype)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilterInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [UserTypeFilter](Models/UserTypeFilter.md) (implementation)

## Methods

### List Operations

#### getRelation

```php
public function getRelation(): ?string

```

Get the optional relation filter for limiting user types. When specified, this filter limits the results to users that have the specified relation to objects of the target type. This allows for more specific filtering beyond just the object type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilterInterface.php#L39)

#### Returns

`string` &#124; `null`

#### getType

```php
public function getType(): string

```

Get the object type to filter by. This specifies the type of objects that users should be related to when filtering results. Only users connected to objects of this type will be included in the filtered results.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilterInterface.php#L50)

#### Returns

`string` — The object type to filter by

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array<'relation'|'type', string>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UserTypeFilterInterface.php#L56)

#### Returns

`array&lt;'relation'` &#124; `'type', string&gt;`
