# TypeDefinition


## Namespace
`OpenFGA\Models`

## Implements
* [TypeDefinitionInterface](Models/TypeDefinitionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;TypeDefinition&#039;` |  |


## Methods
### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\MetadataInterface
```



#### Returns
?[MetadataInterface](Models/MetadataInterface.md)

### getRelations


```php
public function getRelations(): ?OpenFGA\Models\Collections\TypeDefinitionRelationsInterface
```



#### Returns
?[TypeDefinitionRelationsInterface](Models/Collections/TypeDefinitionRelationsInterface.md)

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

### schema

*<small>Implements Models\TypeDefinitionInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

