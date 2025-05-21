# TypeDefinitionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getMetadata

```php
public function getMetadata(): ?[MetadataInterface](Models/MetadataInterface.md)
```



#### Returns
`?[MetadataInterface](Models/MetadataInterface.md)` 

### getRelations

```php
public function getRelations(): ?[TypeDefinitionRelationsInterface](Models/Collections/TypeDefinitionRelationsInterface.md)
```



#### Returns
`?[TypeDefinitionRelationsInterface](Models/Collections/TypeDefinitionRelationsInterface.md)` 

### getType

```php
public function getType(): string
```



#### Returns
`string` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` string, relations?: array&lt;string, array{computed_userset?: array{object?: string, relation?: string}, tuple_to_userset?: array{tupleset: array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}, union?: array&lt;mixed&gt;, intersection?: array&lt;mixed&gt;, difference?: array{base: array&lt;mixed&gt;, subtract: array&lt;mixed&gt;}, direct?: object}&gt;, metadata?: array&lt;&#039;module&#039;|&#039;relations&#039;|&#039;source_info&#039;, array{directly_related_user_types?: array&lt;string, array{condition?: string, relation?: string, type: string, wildcard?: object}&gt;, file?: string, module?: string, source_info?: array{file?: string}}|string&gt;}

