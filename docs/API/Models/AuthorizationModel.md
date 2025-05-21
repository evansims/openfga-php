# AuthorizationModel


## Namespace
`OpenFGA\Models`

## Implements
* [AuthorizationModelInterface](Models/AuthorizationModelInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

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
`array` 

