# AuthorizationModel


## Namespace
`OpenFGA\Models`

## Implements
* [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;AuthorizationModel&#039;` |  |


## Methods
### dsl


```php
public function dsl(): string
```

Return a DSL representation of the model.


#### Returns
string

### getConditions


```php
public function getConditions(): ?OpenFGA\Models\Collections\ConditionsInterface
```

Return the conditions of the model.


#### Returns
?[ConditionsInterface](Models/Collections/ConditionsInterface.md)

### getId


```php
public function getId(): string
```

Return the ID of the model.


#### Returns
string

### getSchemaVersion


```php
public function getSchemaVersion(): OpenFGA\Models\Enums\SchemaVersion
```

Return the schema version of the model.


#### Returns
SchemaVersion

### getTypeDefinitions


```php
public function getTypeDefinitions(): OpenFGA\Models\Collections\TypeDefinitionsInterface
```

Return the type definitions of the model.


#### Returns
[TypeDefinitionsInterface](Models/Collections/TypeDefinitionsInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```

Return a JSON representation of the model.


#### Returns
array

### schema

*<small>Implements Models\AuthorizationModelInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

