# TupleToUsersetV1Interface

Defines a tuple-to-userset operation in authorization models. TupleToUsersetV1 represents an authorization operation that computes usersets by following relationships from one object type to usersets on related objects. This enables complex authorization patterns like &quot;users who can view a document are the editors of the parent folder&quot; or &quot;viewers of a resource are the members of the associated organization&quot;. Use this interface when implementing authorization models that involve indirect relationships through tuple-to-userset operations.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputedUserset


```php
public function getComputedUserset(): ObjectRelationInterface
```

Get the userset that will be computed based on the tupleset. This represents the object-relation pair that defines which userset should be computed for each tuple found in the tupleset. The computed userset determines the final set of users resulting from the tuple-to-userset operation.


#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md)
 The object-relation pair defining the computed userset

### getTupleset


```php
public function getTupleset(): ObjectRelationInterface
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

