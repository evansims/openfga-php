# UsersetTreeTupleToUsersetInterface

Defines a tuple-to-userset operation node in authorization evaluation trees. UsersetTreeTupleToUserset represents a node in the userset evaluation tree that resolves users through tuple-to-userset mappings. This enables complex authorization patterns where access is determined by following relationships from one object to usersets on related objects. Use this interface when working with authorization evaluation trees that contain tuple-to-userset operations, typically returned from expand operations.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getComputed()`](#getcomputed)
  - [`getTupleset()`](#gettupleset)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUsersetInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [UsersetTreeTupleToUserset](Models/UsersetTreeTupleToUserset.md) (implementation)

## Methods

### List Operations

#### getComputed

```php
public function getComputed(): array<int, ComputedInterface>

```

Get the array of computed usersets for the tuple-to-userset operation. This returns a collection of computed userset references that define how to resolve the users from the tuple-to-userset mapping in the tree expansion.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUsersetInterface.php#L30)

#### Returns

`array&lt;`int`, [`ComputedInterface`](ComputedInterface.md)&gt;` — Array of computed userset references

#### getTupleset

```php
public function getTupleset(): string

```

Get the tupleset string identifying which tuples to use for computation. This string identifies the specific tupleset that should be used to resolve users through the tuple-to-userset operation during tree expansion.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUsersetInterface.php#L40)

#### Returns

`string` — The tupleset identifier string

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeTupleToUsersetInterface.php#L46)

#### Returns

`array`
