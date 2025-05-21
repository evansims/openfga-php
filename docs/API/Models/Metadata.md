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
`?string` 

### getRelations


```php
public function getRelations(): ?[RelationMetadataInterface](Models/RelationMetadataInterface.md)
```



#### Returns
`?[RelationMetadataInterface](Models/RelationMetadataInterface.md)` 

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
`array` array{directly_related_user_types?: array&lt;string, array{condition?: string, relation?: string, type: string, wildcard?: object}&gt;, file?: string, module?: string, source_info?: array{file?: string}}|string&gt;

### schema

*<small>Implements Models\MetadataInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

