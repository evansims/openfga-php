# RelationMetadata


## Namespace
`OpenFGA\Models`

## Implements
* [RelationMetadataInterface](Models/RelationMetadataInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;RelationMetadata&#039;` |  |


## Methods
### getDirectlyRelatedUserTypes


```php
public function getDirectlyRelatedUserTypes(): ?OpenFGA\Models\Collections\RelationReferencesInterface
```



#### Returns
?[RelationReferencesInterface](Models/Collections/RelationReferencesInterface.md)

### getModule


```php
public function getModule(): ?string
```



#### Returns
?string

### getSourceInfo


```php
public function getSourceInfo(): ?OpenFGA\Models\SourceInfoInterface
```



#### Returns
?[SourceInfoInterface](Models/SourceInfoInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\RelationMetadataInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

