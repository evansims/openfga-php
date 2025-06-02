# NodeUnionInterface

Represents a union operation between multiple nodes in a userset tree. A node union combines multiple authorization nodes where users from any of the constituent nodes are included in the result set. This implements the logical OR operation in authorization evaluation.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnionInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [NodeUnion](Models/NodeUnion.md) (implementation)



## Methods

                                                
### List Operations
#### getNodes


```php
public function getNodes(): array<int, NodeInterface>
```

Get the collection of nodes that participate in this union. Returns all the nodes that are combined in this union operation. The union result includes users from any of these nodes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnionInterface.php#L26)


#### Returns
array&lt;int, NodeInterface&gt;
 The array of nodes in the union

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the node union to its JSON representation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/NodeUnionInterface.php#L34)


#### Returns
array

