# SuccessInterface

Represents a successful result containing a value. Success results indicate that an operation completed successfully and contain a value of the specified type. They provide type-safe access to successful outcomes while maintaining compatibility with the Result pattern&#039;s fluent interface. Success results behave predictably in all Result operations: - `succeeded()` always returns true - `failed()` always returns false - `val()` returns the contained value safely - `err()` throws since successes have no errors - `success()` executes callbacks with the value - `failure()` skips callbacks and returns unchanged - `then()` applies transformations to the value - `recover()` skips recovery and returns unchanged

## Namespace
`OpenFGA\Results`

## Implements
* [ResultInterface](Results/ResultInterface.md)



## Methods
### err


```php
public function err(): E
```

Retrieves the error from a failed result. This method should only be called on Failure results. Use failed() to check the result type before calling this method to avoid exceptions.


#### Returns
E
 The error that caused the failure

### failed


```php
public function failed(): bool
```

Determines if this result represents a failure.


#### Returns
bool
 True if this is a Failure result, false if it&#039;s a Success

### failure


```php
public function failure(callable $fn): ResultInterface<T, E>
```

Executes a callback when the result is a failure and continues the chain. The callback receives the error as its parameter and is only executed for Failure results. This method always returns the original result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;
 The original result for method chaining

### recover


```php
public function recover(callable $fn): ResultInterface<U, F>
```

Recovers from a failure by transforming it into a success or different failure. The callback is only executed for Failure results and can return either a new Result or a plain value (which becomes a Success). Success results pass through unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;U, F&gt;
 The recovered result or original success

### rethrow


```php
public function rethrow(Throwable|null $throwable = NULL): ResultInterface<T, E>
```

Throws the contained error or continues the chain. For Failure results, this throws either the provided throwable or the contained error. For Success results, this method has no effect and returns the result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$throwable` | Throwable | null | Optional throwable to throw instead of the contained error |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;
 The original result for method chaining

### succeeded


```php
public function succeeded(): bool
```

Determines if this result represents a success.


#### Returns
bool
 True if this is a Success result, false if it&#039;s a Failure

### success


```php
public function success(callable $fn): ResultInterface<T, E>
```

Executes a callback when the result is a success and continues the chain. The callback receives the success value as its parameter and is only executed for Success results. This method always returns the original result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;
 The original result for method chaining

### then


```php
public function then(callable $fn): ResultInterface<U, F>
```

Transforms a successful result using a callback and continues the chain. The callback is only executed for Success results and can return either a new Result or a plain value (which becomes a Success). Failure results pass through unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;U, F&gt;
 The transformed result or original failure

### unwrap


```php
public function unwrap(?callable $fn = NULL): mixed
```

Extracts the value from the result or applies a transformation. Without a callback, this returns the success value or throws the failure error. With a callback, the function is called with either the success value or failure error, and its return value is returned instead of throwing.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | ?callable |  |

#### Returns
mixed
 The success value, callback result, or throws the error

### val


```php
public function val(): T
```

Retrieves the value from a successful result. This method should only be called on Success results. Use succeeded() to check the result type before calling this method to avoid exceptions.


#### Returns
T
 The successful value

