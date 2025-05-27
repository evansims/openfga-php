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
public function getConditions(): null | ConditionsInterface<ConditionInterface>
```

Return the conditions of the model.


#### Returns
`null | ConditionsInterface<ConditionInterface>`

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
public function getTypeDefinitions(): TypeDefinitionsInterface<TypeDefinitionInterface>
```

Return the type definitions of the model.


#### Returns
`TypeDefinitionsInterface<TypeDefinitionInterface>`

### jsonSerialize


```php
public function jsonSerialize(): array
```

Return a JSON representation of the model.


#### Returns
`array`

