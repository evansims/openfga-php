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
`array`
 array{directly_related_user_types?: array&lt;string, array{condition?: string, relation?: string, type: string, wildcard?: object}&gt;, file?: string, module?: string, source_info?: array{file?: string}}|string&gt;

