# BatchCheckItemInterface

Represents a single item in a batch check request. Each batch check item contains a tuple key to check, an optional context, optional contextual tuples, and a correlation ID to map the result back to this specific check. The correlation ID must be unique within the batch and follow the pattern: alphanumeric characters or hyphens, maximum 36 characters.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItemInterface.php)

## Implements
* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes
* [BatchCheckItem](Models/BatchCheckItem.md) (implementation)



## Methods

                                                                                    
### List Operations
#### getContext


```php
public function getContext(): ?object
```

Get the context object for this check. This provides additional context data that can be used by conditions in the authorization model during evaluation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItemInterface.php#L31)


#### Returns
`object` &#124; `null`
#### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the contextual tuples for this check. These are additional tuples that are evaluated only for this specific check and are not persisted in the store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItemInterface.php#L41)


#### Returns
[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`
#### getCorrelationId


```php
public function getCorrelationId(): string
```

Get the correlation ID for this batch check item. This unique identifier maps the result back to this specific check. Must be alphanumeric characters or hyphens, maximum 36 characters.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItemInterface.php#L51)


#### Returns
`string` — The correlation ID
#### getTupleKey


```php
public function getTupleKey(): TupleKeyInterface
```

Get the tuple key to be checked. This defines the user, relation, and object for the authorization check.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckItemInterface.php#L60)


#### Returns
[`TupleKeyInterface`](TupleKeyInterface.md) — The tuple key for this check
### Other
#### jsonSerialize


```php
public function jsonSerialize()
```





