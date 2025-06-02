# UsersetTreeInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getRoot


```php
public function getRoot(): NodeInterface
```

Get the root node of the userset tree structure. This returns the top-level node that represents the entry point for userset expansion. The tree structure allows for complex authorization logic including unions, intersections, and difference operations.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The root node of the userset tree

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```



#### Returns
array&lt;string, mixed&gt;

