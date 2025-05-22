# AssertionTupleKey


## Namespace
`OpenFGA\Models`

## Implements
* [AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;AssertionTupleKey&#039;` |  |


## Methods
### getObject


```php
public function getObject(): string
```



#### Returns
`string`

### getRelation


```php
public function getRelation(): string
```



#### Returns
`string`

### getUser


```php
public function getUser(): string
```



#### Returns
`string`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
```php
user: string,
relation: string,
object: string,
}
```

### schema

*<small>Implements Models\AssertionTupleKeyInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

