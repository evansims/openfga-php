# Store


## Namespace
`OpenFGA\Models`

## Implements
* [StoreInterface](Models/StoreInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Store&#039;` |  |


## Methods
### getCreatedAt


```php
public function getCreatedAt(): DateTimeInterface
```



#### Returns
DateTimeInterface

### getDeletedAt


```php
public function getDeletedAt(): ?DateTimeInterface
```



#### Returns
?DateTimeInterface

### getId


```php
public function getId(): string
```



#### Returns
string

### getName


```php
public function getName(): string
```



#### Returns
string

### getUpdatedAt


```php
public function getUpdatedAt(): DateTimeInterface
```



#### Returns
DateTimeInterface

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\StoreInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

