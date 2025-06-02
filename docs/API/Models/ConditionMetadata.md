# ConditionMetadata

Contains metadata information about conditions in your authorization model. ConditionMetadata provides context about ABAC (Attribute-Based Access Control) conditions, including module organization and source information for debugging. This helps you understand where conditions are defined and how they&#039;re structured within your authorization model. Use this when working with conditional authorization rules that depend on runtime attributes and context data.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadata.php)

## Implements
* [ConditionMetadataInterface](ConditionMetadataInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Related Classes
* [ConditionMetadataInterface](Models/ConditionMetadataInterface.md) (interface)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;ConditionMetadata&#039;` |  |


## Methods

                                                                                    
### List Operations
#### getModule


```php
public function getModule(): string
```

Get the module name where the condition is defined. This provides organizational information about which module or namespace contains the condition definition, helping with debugging and understanding the model structure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadata.php#L56)


#### Returns
string
 The module name containing the condition

#### getSourceInfo


```php
public function getSourceInfo(): OpenFGA\Models\SourceInfoInterface
```

Get source file information for debugging and tooling. This provides information about the source file where the condition was originally defined, which is useful for development tools, debugging, and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadata.php#L65)


#### Returns
OpenFGA\Models\SourceInfoInterface
 The source file information

### Model Management
#### schema

*<small>Implements Models\ConditionMetadataInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionMetadata.php#L74)


#### Returns
array

