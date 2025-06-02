# ResultInterface

Represents the result of an operation that can either succeed or fail. The Result pattern provides a safe and composable way to handle operations that might fail without using exceptions for control flow. Results can be chained together using fluent methods, making error handling explicit and predictable. ## Working with Result Types Each Result contains either a success value (specific response interface) or a failure error (Throwable). The specific types are documented in each method&#039;s @return annotation. ## Common Usage Patterns ### Simple Value Extraction ```php $result = $client-&gt;check($store, $model, $tupleKey); if ($result-&gt;succeeded()) { $response = $result-&gt;val(); // Returns CheckResponseInterface $allowed = $response-&gt;getAllowed(); } ``` ### Fluent Error Handling ```php $allowed = $client-&gt;check($store, $model, $tupleKey) -&gt;success(fn($response) =&gt; logger()-&gt;info(&#039;Check succeeded&#039;)) -&gt;failure(fn($error) =&gt; logger()-&gt;error(&#039;Check failed: &#039; . $error-&gt;getMessage())) -&gt;then(fn($response) =&gt; $response-&gt;getAllowed()) -&gt;recover(fn($error) =&gt; false) // Default to not allowed on error -&gt;unwrap(); ``` ### Safe Unwrapping with Default Values ```php $store = $client-&gt;getStore($storeId) -&gt;unwrap(fn($result) =&gt; $result instanceof StoreInterface ? $result : null); ``` ### Transforming Results ```php $storeNames = $client-&gt;listStores() -&gt;then(fn($response) =&gt; array_map( fn($store) =&gt; $store-&gt;getName(), $response-&gt;getStores()-&gt;toArray() )) -&gt;unwrap(); ```

## Namespace
`OpenFGA\Results`




## Methods
### err


```php
public function err(): Throwable
```

Retrieves the error from a failed result. This method should only be called on Failure results. Use failed() to check the result type before calling this method to avoid exceptions.


#### Returns
Throwable
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
public function failure(callable $fn): ResultInterface
```

Executes a callback when the result is a failure and continues the chain. The callback receives the error as its parameter and is only executed for Failure results. This method always returns the original result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
ResultInterface
 The original result for method chaining

### recover


```php
public function recover(callable $fn): ResultInterface
```

Recovers from a failure by transforming it into a success or different failure. The callback is only executed for Failure results and can return either a new Result or a plain value (which becomes a Success). Success results pass through unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
ResultInterface
 The recovered result or original success

### rethrow


```php
public function rethrow(Throwable|null $throwable = NULL): ResultInterface
```

Throws the contained error or continues the chain. For Failure results, this throws either the provided throwable or the contained error. For Success results, this method has no effect and returns the result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$throwable` | Throwable|null | Optional throwable to throw instead of the contained error |

#### Returns
ResultInterface
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
public function success(callable $fn): ResultInterface
```

Executes a callback when the result is a success and continues the chain. The callback receives the success value (specific response interface) as its parameter and is only executed for Success results. This method always returns the original result unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
ResultInterface
 The original result for method chaining

### then


```php
public function then(callable $fn): ResultInterface
```

Transforms a successful result using a callback and continues the chain. The callback is only executed for Success results and receives the specific response interface as its parameter. It can return either a new Result or a plain value (which becomes a Success). Failure results pass through unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
ResultInterface
 The transformed result or original failure

### unwrap


```php
public function unwrap(?callable $fn = NULL): mixed
```

Extracts the value from the result or applies a transformation. Without a callback, this returns the success value (specific response interface) or throws the failure error. With a callback, the function is called with either the response interface or failure error, and its return value is returned instead of throwing.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | ?callable |  |

#### Returns
mixed
 The response interface, callback result, or throws the error

### val


```php
public function val(): mixed
```

Retrieves the value from a successful result. This method should only be called on Success results. Use succeeded() to check the result type before calling this method to avoid exceptions. Returns the specific response interface documented in the calling method&#039;s @return annotation.


#### Returns
mixed
 The response interface (e.g., CheckResponseInterface, StoreInterface)

