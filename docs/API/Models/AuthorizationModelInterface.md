# AuthorizationModelInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getConditions


```php
public function getConditions(): ?[ConditionsInterface](Models/Collections/ConditionsInterface.md)
```



#### Returns
`?[ConditionsInterface](Models/Collections/ConditionsInterface.md)` 

### getId


```php
public function getId(): string
```



#### Returns
`string` 

### getSchemaVersion


```php
public function getSchemaVersion(): SchemaVersion
```



#### Returns
`SchemaVersion` 

### getTypeDefinitions


```php
public function getTypeDefinitions(): [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)
```



#### Returns
`[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` id: string, schema_version: string, type_definitions: array&lt;int, array{type: string, relations?: array&lt;string, mixed&gt;, metadata?: array&lt;string, mixed&gt;}&gt;, conditions?: array&lt;int, array{name: string, expression: string, parameters?: array&lt;string, mixed&gt;, metadata?: array&lt;string, mixed&gt;}&gt; }

