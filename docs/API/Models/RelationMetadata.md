# RelationMetadata


## Namespace
`OpenFGA\Models`

## Implements
* [RelationMetadataInterface](Models/RelationMetadataInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

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

### schema

*<small>Implements Models\RelationMetadataInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

