# AssertionInterface

Represents an assertion used to test authorization model correctness. Assertions are test cases that verify whether specific authorization decisions should be allowed or denied. They are essential for validating authorization models and ensuring they behave as expected. Each assertion includes a tuple key to test, the expected result, and optional contextual information for complex scenarios.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [Assertion](Models/Assertion.md) (implementation)



## Methods

                                                                                    
### List Operations
#### getContext


```php
public function getContext(): ?array
```

Get the context data for evaluating ABAC conditions. Context provides additional information that can be used when evaluating attribute-based access control (ABAC) conditions. This might include user attributes, resource properties, or environmental factors like time of day.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php#L32)


#### Returns
array &#124; null

#### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```

Get the contextual tuples for this assertion. Contextual tuples provide additional relationship data that should be considered when evaluating the assertion. These are temporary relationships that exist only for the duration of the authorization check, useful for testing &quot;what-if&quot; scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php#L44)


#### Returns
[TupleKeysInterface](Models/Collections/TupleKeysInterface.md) &#124; null

#### getExpectation


```php
public function getExpectation(): bool
```

Get the expected result for this assertion. The expectation defines whether the authorization check should return true (access granted) or false (access denied). This is what the assertion will be tested against.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php#L55)


#### Returns
bool
 True if access should be granted, false if access should be denied

#### getTupleKey


```php
public function getTupleKey(): AssertionTupleKeyInterface
```

Get the tuple key that defines what to test. The tuple key specifies the exact authorization question to ask: &quot;Does user X have relation Y on object Z?&quot; This is the core of what the assertion is testing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php#L66)


#### Returns
[AssertionTupleKeyInterface](AssertionTupleKeyInterface.md)
 The tuple key defining the authorization question

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/AssertionInterface.php#L77)


#### Returns
array

