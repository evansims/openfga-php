# Condition


## Namespace
`OpenFGA\Models`

## Implements
* [ConditionInterface](Models/ConditionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Condition&#039;` |  |


## Methods
### getExpression


```php
public function getExpression(): string
```

Get the condition expression. This returns the logical expression that defines when this condition evaluates to true. The expression can reference parameters and context data to enable dynamic authorization decisions based on runtime information.


#### Returns
string
 The condition expression defining the evaluation logic

### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\ConditionMetadataInterface
```

Get metadata about the condition definition. This provides additional information about where the condition was defined and how it should be processed, which is useful for tooling and debugging.


#### Returns
?[ConditionMetadataInterface](Models/ConditionMetadataInterface.md)
 The condition metadata, or null if not provided

### getName


```php
public function getName(): string
```

Get the name of the condition. This is a unique identifier for the condition within the authorization model, allowing it to be referenced from type definitions and other parts of the model.


#### Returns
string
 The unique name identifying this condition

### getParameters


```php
public function getParameters(): ?OpenFGA\Models\Collections\ConditionParametersInterface
```

Get the parameters available to the condition expression. These parameters define the typed inputs that can be used within the condition expression, enabling dynamic evaluation based on contextual data provided during authorization checks.


#### Returns
?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)
 The condition parameters, or null if the condition uses no parameters

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the condition for JSON encoding. This method prepares the condition data for API requests or storage, ensuring all components are properly formatted according to the OpenFGA API specification.


#### Returns
array

### schema

*<small>Implements Models\ConditionInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

