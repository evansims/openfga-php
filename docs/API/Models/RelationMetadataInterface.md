# RelationMetadataInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getDirectlyRelatedUserTypes


```php
public function getDirectlyRelatedUserTypes(): null|RelationReferencesInterface<RelationReferenceInterface>
```



#### Returns
null | RelationReferencesInterface&lt;[RelationReferenceInterface](Models/RelationReferenceInterface.md)&gt;

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

