# Metadata


## Namespace
`OpenFGA\Models`

## Implements
* [MetadataInterface](Models/MetadataInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)



## Methods
### getModule


```php
public function getModule(): ?string
```



#### Returns
?string

### getRelations


```php
public function getRelations(): ?OpenFGA\Models\Collections\RelationMetadataCollection
```



#### Returns
?[RelationMetadataCollection](Models/Collections/RelationMetadataCollection.md)

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

*<small>Implements Models\MetadataInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

