# UsersetTreeInterface

Defines a tree structure for representing complex userset operations. UsersetTree provides a hierarchical representation of authorization evaluation logic, where each node can contain unions, intersections, differences, computed usersets, or tuple-to-userset operations. This tree structure enables OpenFGA to represent and evaluate sophisticated authorization patterns efficiently. Use this interface when working with authorization evaluation trees returned by expand operations or when implementing custom authorization logic that needs to traverse userset structures.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getRoot()`](#getroot)
  - [`jsonSerialize()`](#jsonserialize)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeInterface.php)

## Implements

- [`ModelInterface`](ModelInterface.md)
- `JsonSerializable`

## Related Classes

- [UsersetTree](Models/UsersetTree.md) (implementation)

## Methods

### getRoot

```php
public function getRoot(): NodeInterface

```

Get the root node of the userset tree structure. This returns the top-level node that represents the entry point for userset expansion. The tree structure allows for complex authorization logic including unions, intersections, and difference operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeInterface.php#L33)

#### Returns

[`NodeInterface`](NodeInterface.md) — The root node of the userset tree

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTreeInterface.php#L39)

#### Returns

`array&lt;`string`, `mixed`&gt;`
