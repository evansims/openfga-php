# RelationMetadata

Contains metadata information about a relation in your authorization model. RelationMetadata provides additional context about how a relation behaves, including which user types can be directly assigned to it and source information for debugging. This helps with model validation and provides insights into your authorization structure. Use this when you need to understand the constraints and properties of specific relations in your authorization model.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadata.php)

## Implements
* [RelationMetadataInterface](RelationMetadataInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;RelationMetadata&#039;` |  |


## Methods
### getDirectlyRelatedUserTypes


```php
public function getDirectlyRelatedUserTypes(): ?OpenFGA\Models\Collections\RelationReferencesInterface
```

Get the user types that can be directly related through this relation. This defines which types of users can have this relation to objects, providing type safety and helping with authorization model validation. For example, a &quot;member&quot; relation might allow &quot;user&quot; and &quot;group&quot; types.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadata.php#L60)


#### Returns
?OpenFGA\Models\Collections\RelationReferencesInterface
 The directly related user types, or null if not specified

### getModule


```php
public function getModule(): ?string
```

Get the optional module name for organization. This provides organizational information about which module or namespace contains the relation definition, helping with model organization and debugging.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadata.php#L69)


#### Returns
?string
 The module name, or null if not specified

### getSourceInfo


```php
public function getSourceInfo(): ?OpenFGA\Models\SourceInfoInterface
```

Get optional source file information for debugging and tooling. This provides information about the source file where the relation was originally defined, which is useful for development tools, debugging, and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadata.php#L78)


#### Returns
?OpenFGA\Models\SourceInfoInterface
 The source file information, or null if not available

### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationMetadata.php#L87)


#### Returns
array

### schema

*<small>Implements Models\RelationMetadataInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

