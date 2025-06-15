# UsersetTree

Represents the evaluation tree for determining user access. When OpenFGA evaluates whether a user has access to an object, it builds a tree structure showing all the authorization paths that were considered. The UsersetTree contains this evaluation tree with a root node that represents the starting point of the access evaluation. This is primarily used for debugging authorization decisions and understanding why access was granted or denied in complex permission scenarios.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`getRoot()`](#getroot)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTree.php)

## Implements

- [`UsersetTreeInterface`](UsersetTreeInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [UsersetTreeInterface](Models/UsersetTreeInterface.md) (interface)

## Constants

| Name            | Value         | Description |
| --------------- | ------------- | ----------- |
| `OPENAPI_MODEL` | `UsersetTree` |             |

## Methods

### getRoot

```php
public function getRoot(): OpenFGA\Models\NodeInterface

```

Get the root node of the userset tree structure. This returns the top-level node that represents the entry point for userset expansion. The tree structure allows for complex authorization logic including unions, intersections, and difference operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTree.php#L53)

#### Returns

[`NodeInterface`](NodeInterface.md) — The root node of the userset tree

### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersetTree.php#L62)

#### Returns

`array`

### schema

*<small>Implements Models\UsersetTreeInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model
