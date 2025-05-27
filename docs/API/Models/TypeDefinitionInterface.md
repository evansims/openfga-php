# TypeDefinitionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\MetadataInterface
```



#### Returns
?[MetadataInterface](Models/MetadataInterface.md)

### getRelations


```php
public function getRelations(): null|TypeDefinitionRelationsInterface<UsersetInterface>
```



#### Returns
null | TypeDefinitionRelationsInterface&lt;[UsersetInterface](Models/UsersetInterface.md)&gt;

### getType


```php
public function getType(): string
```



#### Returns
string

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

