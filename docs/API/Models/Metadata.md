# Metadata

Contains metadata information about type definitions in your authorization model. Metadata provides additional context about how your authorization types behave, including module information, relation constraints, and source details. This information helps with model validation, debugging, and understanding the structure of your authorization system. Use this when you need insights into the properties and constraints of your authorization model&#039;s type definitions.

## Namespace
`OpenFGA\Models`

## Implements
* [MetadataInterface](MetadataInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Metadata&#039;` |  |


## Methods
### getModule


```php
public function getModule(): ?string
```

Get the module name for this metadata. Modules provide a way to organize and namespace authorization model components, similar to packages in programming languages. This helps with model organization and prevents naming conflicts in large authorization systems.


#### Returns
?string
 The module name, or null if not specified

### getRelations


```php
public function getRelations(): ?OpenFGA\Models\Collections\RelationMetadataCollection
```

Get the collection of relation metadata. Relation metadata provides additional configuration and context for specific relations within a type definition. This can include documentation, constraints, or other relation-specific settings that enhance the authorization model.


#### Returns
?OpenFGA\Models\Collections\RelationMetadataCollection
 The relation metadata collection, or null if not specified

### getSourceInfo


```php
public function getSourceInfo(): ?OpenFGA\Models\SourceInfoInterface
```

Get the source code information for this metadata. Source information provides debugging and development context by tracking where authorization model elements were defined. This is particularly useful for development tools and error reporting.


#### Returns
?OpenFGA\Models\SourceInfoInterface
 The source information, or null if not available

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\MetadataInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

