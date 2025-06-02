# TupleToUsersetV1

Represents a tuple-to-userset relationship that derives permissions from related objects. This enables complex authorization patterns where permissions on one object are determined by relationships with other objects. For example, &quot;users who can edit a document are those who are owners of the folder containing it&quot;. The tupleset defines which related objects to look at, and computedUserset specifies which relationship on those objects grants the permission.

## Namespace
`OpenFGA\Models`

## Implements
* [TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;v1.TupleToUserset&#039;` |  |


## Methods
### getComputedUserset


```php
public function getComputedUserset(): OpenFGA\Models\ObjectRelationInterface
```

Get the userset that will be computed based on the tupleset. This represents the object-relation pair that defines which userset should be computed for each tuple found in the tupleset. The computed userset determines the final set of users resulting from the tuple-to-userset operation.


#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md)
 The object-relation pair defining the computed userset

### getTupleset


```php
public function getTupleset(): OpenFGA\Models\ObjectRelationInterface
```

Get the tupleset (object-relation pair) that defines which tuples to use for computation. This represents the object-relation pair that identifies which tuples should be examined to compute the final userset. For each matching tuple, the computed userset will be evaluated to determine the resulting users.


#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md)
 The object-relation pair defining the tupleset

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TupleToUsersetV1Interface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

