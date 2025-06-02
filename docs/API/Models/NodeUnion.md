# NodeUnion

Represents a union of multiple nodes in an authorization model tree. When OpenFGA evaluates complex authorization rules, it often needs to combine results from multiple authorization paths. A NodeUnion contains a collection of nodes that should be evaluated together, typically representing an OR relationship where access is granted if any of the contained nodes grants access. This is commonly used in authorization model structures where a user can have access through multiple different permission paths.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnion.php)

## Implements

* [`NodeUnionInterface`](NodeUnionInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [NodeUnionInterface](Models/NodeUnionInterface.md) (interface)

## Constants

| Name            | Value         | Description |
| --------------- | ------------- | ----------- |
| `OPENAPI_MODEL` | `'NodeUnion'` |             |

## Methods

### List Operations

#### getNodes

```php
public function getNodes(): array<int, NodeInterface>

```

Get the collection of nodes that participate in this union. Returns all the nodes that are combined in this union operation. The union result includes users from any of these nodes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnion.php#L55)

#### Returns

`array&lt;`int`, [`NodeInterface`](NodeInterface.md)&gt;` — The array of nodes in the union

### Model Management

#### schema

*<small>Implements Models\NodeUnionInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the node union to its JSON representation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnion.php#L64)

#### Returns

`array`
