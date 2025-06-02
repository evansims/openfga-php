# NodeUnion

Represents a union of multiple nodes in an authorization model tree. When OpenFGA evaluates complex authorization rules, it often needs to combine results from multiple authorization paths. A NodeUnion contains a collection of nodes that should be evaluated together, typically representing an OR relationship where access is granted if any of the contained nodes grants access. This is commonly used in authorization model structures where a user can have access through multiple different permission paths.

## Namespace
`OpenFGA\Models`

## Implements
* [NodeUnionInterface](Models/NodeUnionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;NodeUnion&#039;` |  |


## Methods
### getNodes


```php
public function getNodes(): array<int, NodeInterface>
```

Get the collection of nodes that participate in this union. Returns all the nodes that are combined in this union operation. The union result includes users from any of these nodes.


#### Returns
array&lt;int, [NodeInterface](Models/NodeInterface.md)&gt;
 The array of nodes in the union

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the node union to its JSON representation.


#### Returns
array

### schema

*<small>Implements Models\NodeUnionInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

