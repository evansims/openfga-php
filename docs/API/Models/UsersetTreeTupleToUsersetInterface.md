# UsersetTreeTupleToUsersetInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputed


```php
public function getComputed(): [ComputedsInterface](Models/Collections/ComputedsInterface.md)
```



#### Returns
`[ComputedsInterface](Models/Collections/ComputedsInterface.md)`

### getTupleset


```php
public function getTupleset(): string
```



#### Returns
`string`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 string, computed: array&lt;int, array{userset: string}&gt;}

