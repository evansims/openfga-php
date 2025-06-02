# BatchCheckItem

Represents a single item in a batch check request. Each batch check item contains a tuple key to check, an optional context, optional contextual tuples, and a correlation ID to map the result back to this specific check.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php)

## Implements
* [BatchCheckItemInterface](BatchCheckItemInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)



## Methods
### getContext


```php
public function getContext(): ?object
```

Get the context object for this check. This provides additional context data that can be used by conditions in the authorization model during evaluation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L187)


#### Returns
?object

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the contextual tuples for this check. These are additional tuples that are evaluated only for this specific check and are not persisted in the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L196)


#### Returns
?OpenFGA\Models\Collections\TupleKeysInterface

### getCorrelationId


```php
public function getCorrelationId(): string
```

Get the correlation ID for this batch check item. This unique identifier maps the result back to this specific check. Must be alphanumeric characters or hyphens, maximum 36 characters.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L205)


#### Returns
string
 The correlation ID

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface
```

Get the tuple key to be checked. This defines the user, relation, and object for the authorization check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L214)


#### Returns
OpenFGA\Models\TupleKeyInterface
 The tuple key for this check

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L225)


#### Returns
array&lt;string, mixed&gt;

### schema

*<small>Implements Models\BatchCheckItemInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

### toArray


```php
public function toArray(): array<string, mixed>
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItem.php#L240)


#### Returns
array&lt;string, mixed&gt;

