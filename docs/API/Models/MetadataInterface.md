# MetadataInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



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

