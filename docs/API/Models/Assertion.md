# Assertion


## Namespace
`OpenFGA\Models`

## Implements
* [AssertionInterface](Models/AssertionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Assertion&#039;` |  |


## Methods
### getContext


```php
public function getContext(): ?array
```

Get the context data for evaluating ABAC conditions. Context provides additional information that can be used when evaluating attribute-based access control (ABAC) conditions. This might include user attributes, resource properties, or environmental factors like time of day.


#### Returns
?array

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the contextual tuples for this assertion. Contextual tuples provide additional relationship data that should be considered when evaluating the assertion. These are temporary relationships that exist only for the duration of the authorization check, useful for testing &quot;what-if&quot; scenarios.


#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

### getExpectation


```php
public function getExpectation(): bool
```

Get the expected result for this assertion. The expectation defines whether the authorization check should return true (access granted) or false (access denied). This is what the assertion will be tested against.


#### Returns
bool
 True if access should be granted, false if access should be denied

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\AssertionTupleKeyInterface
```

Get the tuple key that defines what to test. The tuple key specifies the exact authorization question to ask: &quot;Does user X have relation Y on object Z?&quot; This is the core of what the assertion is testing.


#### Returns
[AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)
 The tuple key defining the authorization question

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\AssertionInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

