# UsersetTree


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetTreeInterface](Models/UsersetTreeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersetTree&#039;` |  |


## Methods
### getRoot


```php
public function getRoot(): OpenFGA\Models\NodeInterface
```

Get the root node of the userset tree structure. This returns the top-level node that represents the entry point for userset expansion. The tree structure allows for complex authorization logic including unions, intersections, and difference operations.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The root node of the userset tree

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetTreeInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

