# RelationMetadataInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getDirectlyRelatedUserTypes


```php
public function getDirectlyRelatedUserTypes(): ?[RelationReferencesInterface](Models/Collections/RelationReferencesInterface.md)
```



#### Returns
`?[RelationReferencesInterface](Models/Collections/RelationReferencesInterface.md)` 

### getModule


```php
public function getModule(): ?string
```



#### Returns
`?string` 

### getSourceInfo


```php
public function getSourceInfo(): ?[SourceInfoInterface](Models/SourceInfoInterface.md)
```



#### Returns
`?[SourceInfoInterface](Models/SourceInfoInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` string, directly_related_user_types?: array&lt;string, array{type: string, relation?: string, wildcard?: object, condition?: string}&gt;, source_info?: array{file?: string}}

