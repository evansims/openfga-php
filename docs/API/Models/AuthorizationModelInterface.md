# AuthorizationModelInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### dsl


```php
public function dsl(): string
```

Return a DSL representation of the model.


#### Returns
`string` 

### getConditions


```php
public function getConditions(): ?[ConditionsInterface](Models/Collections/ConditionsInterface.md)
```

Return the conditions of the model.


#### Returns
`?[ConditionsInterface](Models/Collections/ConditionsInterface.md)` 

### getId


```php
public function getId(): string
```

Return the ID of the model.


#### Returns
`string` 

### getSchemaVersion


```php
public function getSchemaVersion(): SchemaVersion
```

Return the schema version of the model.


#### Returns
`SchemaVersion` 

### getTypeDefinitions


```php
public function getTypeDefinitions(): [TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)
```

Return the type definitions of the model.


#### Returns
`[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```

Return a JSON representation of the model.


#### Returns
`array` id: string, schema_version: string, type_definitions: array&lt;int, array{type: string, relations?: array&lt;string, mixed&gt;, metadata?: array&lt;string, mixed&gt;}&gt;, conditions?: array&lt;int, array{name: string, expression: string, parameters?: array&lt;string, mixed&gt;, metadata?: array&lt;string, mixed&gt;}&gt; }

