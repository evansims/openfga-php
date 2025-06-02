# TupleChangeInterface

Represents a change event for a relationship tuple in OpenFGA. Tuple changes capture the history of relationship modifications in the authorization store. Each change records whether a tuple was written (created) or deleted, along with the timestamp and the specific tuple that was affected. These change events are essential for: - Auditing relationship modifications - Implementing consistency across distributed systems - Debugging authorization issues - Maintaining change history for compliance

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getOperation


```php
public function getOperation(): TupleOperation
```

Get the type of operation performed on the tuple. Operations indicate whether the tuple was written (created) or deleted from the authorization store. This information is crucial for understanding the nature of the change.


#### Returns
TupleOperation
 The operation type (write or delete)

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```

Get the timestamp when this tuple change occurred. Timestamps help track the chronological order of changes and provide audit trail capabilities. They are essential for understanding the sequence of relationship modifications.


#### Returns
DateTimeImmutable
 The change timestamp

### getTupleKey


```php
public function getTupleKey(): TupleKeyInterface
```

Get the tuple key that was affected by this change. The tuple key identifies which specific relationship was created or deleted, containing the user, relation, object, and optional condition information.


#### Returns
TupleKeyInterface
 The tuple key that was modified

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```



#### Returns
array&lt;string, mixed&gt;

