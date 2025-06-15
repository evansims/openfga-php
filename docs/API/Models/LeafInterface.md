# LeafInterface

Represents a leaf node in OpenFGA&#039;s userset tree structure. Leaf nodes are terminal nodes in the authorization evaluation tree that define the actual users or user resolution methods. They represent the final step in determining who has access to a particular resource through a specific relation. A leaf can specify users through one of three mechanisms: 1. **Direct users**: An explicit list of user identifiers 2. **Computed userset**: A reference to another userset to be evaluated 3. **Tuple-to-userset**: A complex resolution that follows tuple relationships Only one of these mechanisms should be active in any given leaf node, as they represent different strategies for determining the final user set.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getComputed()`](#getcomputed)
  - [`getTupleToUserset()`](#gettupletouserset)
  - [`getUsers()`](#getusers)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/LeafInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [Leaf](Models/Leaf.md) (implementation)

## Methods

### getComputed

```php
public function getComputed(): ComputedInterface|null

```

Get the computed userset specification for this leaf. When present, this defines a computed relationship that resolves to other usersets dynamically. This allows for indirect relationships where users are determined by following other relations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/LeafInterface.php#L41)

#### Returns

[`ComputedInterface`](ComputedInterface.md) &#124; `null` — The computed userset specification, or null if not used

### getTupleToUserset

```php
public function getTupleToUserset(): UsersetTreeTupleToUsersetInterface|null

```

Get the tuple-to-userset operation for this leaf. When present, this defines how to compute users by examining tuples and resolving them to usersets. This enables complex relationship patterns where users are derived from tuple relationships.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/LeafInterface.php#L52)

#### Returns

[`UsersetTreeTupleToUsersetInterface`](UsersetTreeTupleToUsersetInterface.md) &#124; `null` — The tuple-to-userset operation, or null if not used

### getUsers

```php
public function getUsers(): UsersListInterface|null

```

Get the direct list of users for this leaf node. When present, this provides an explicit list of users who have access through this leaf. This is used for direct user assignments rather than computed or derived access patterns.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/LeafInterface.php#L63)

#### Returns

[`UsersListInterface`](Models/Collections/UsersListInterface.md) &#124; `null` — The list of users with direct access, or null if not used

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/LeafInterface.php#L69)

#### Returns

`array&lt;`string`, `mixed`&gt;`
