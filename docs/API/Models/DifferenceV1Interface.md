# DifferenceV1Interface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

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

computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }, subtract: array{ computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }}


#### Returns
`array` array{ computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }, subtract: array{ computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object, }}

