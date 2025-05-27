# TypeDefinitionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getMetadata


```php
public function getMetadata(): ?[MetadataInterface](Models/MetadataInterface.md)
```



#### Returns
`?[MetadataInterface](Models/MetadataInterface.md)`

### getRelations


```php
public function getRelations(): null | TypeDefinitionRelationsInterface<UsersetInterface>
```



#### Returns
`null | TypeDefinitionRelationsInterface<UsersetInterface>`

### getType


```php
public function getType(): string
```



#### Returns
`string`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

