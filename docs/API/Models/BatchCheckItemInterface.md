# BatchCheckItemInterface

Represents a single item in a batch check request. Each batch check item contains a tuple key to check, an optional context, optional contextual tuples, and a correlation ID to map the result back to this specific check. The correlation ID must be unique within the batch and follow the pattern: alphanumeric characters or hyphens, maximum 36 characters.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getContext


```php
public function getContext(): ?object
```

Get the context object for this check. This provides additional context data that can be used by conditions in the authorization model during evaluation.


#### Returns
?object

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the contextual tuples for this check. These are additional tuples that are evaluated only for this specific check and are not persisted in the store.


#### Returns
?OpenFGA\Models\Collections\TupleKeysInterface

### getCorrelationId


```php
public function getCorrelationId(): string
```

Get the correlation ID for this batch check item. This unique identifier maps the result back to this specific check. Must be alphanumeric characters or hyphens, maximum 36 characters.


#### Returns
string
 The correlation ID

### getTupleKey


```php
public function getTupleKey(): TupleKeyInterface
```

Get the tuple key to be checked. This defines the user, relation, and object for the authorization check.


#### Returns
TupleKeyInterface
 The tuple key for this check

### jsonSerialize


```php
public function jsonSerialize()
```




