# DifferenceV1


## Namespace
`OpenFGA\Models`

## Implements
* [DifferenceV1Interface](Models/DifferenceV1Interface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Methods
### getBase


```php
public function getBase(): [UsersetInterface](Models/UsersetInterface.md)
```



#### Returns
`[UsersetInterface](Models/UsersetInterface.md)` 

### getSubtract


```php
public function getSubtract(): [UsersetInterface](Models/UsersetInterface.md)
```



#### Returns
`[UsersetInterface](Models/UsersetInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` array{ computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }, subtract: array{ computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }}

### schema

*<small>Implements Models\DifferenceV1Interface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

