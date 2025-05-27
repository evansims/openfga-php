# StoreInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



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
public function jsonSerialize(): array<'created_at'|'deleted_at'|'id'|'name'|'updated_at', string>
```



#### Returns
array&lt;'created_at' | 'deleted_at' | 'id' | 'name' | 'updated_at', string&gt;

