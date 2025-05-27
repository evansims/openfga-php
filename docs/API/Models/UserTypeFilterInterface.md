# UserTypeFilterInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
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

### jsonSerialize


```php
public function jsonSerialize(): array<'relation' | 'type', string>
```



#### Returns
`array<'relation' | 'type', string>`

