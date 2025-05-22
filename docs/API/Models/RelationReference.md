# RelationReference


## Namespace
`OpenFGA\Models`

## Implements
* [RelationReferenceInterface](Models/RelationReferenceInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;RelationReference&#039;` |  |


## Methods
### getCondition


```php
public function getCondition(): ?string
```



#### Returns
`?string`

### getRelation


```php
public function getRelation(): ?string
```



#### Returns
`?string`

### getType


```php
public function getType(): string
```



#### Returns
`string`

### getWildcard


```php
public function getWildcard(): ?object
```



#### Returns
`?object`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 string, relation?: string, wildcard?: object, condition?: string}

### schema

*<small>Implements Models\RelationReferenceInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

