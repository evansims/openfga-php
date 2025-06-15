# Success

Concrete implementation of a successful result containing a value. This class represents the successful outcome of an operation, storing the resulting value and providing type-safe access through the Result pattern&#039;s fluent interface.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`err()`](#err)
  - [`failed()`](#failed)
  - [`failure()`](#failure)
  - [`recover()`](#recover)
  - [`rethrow()`](#rethrow)
  - [`succeeded()`](#succeeded)
  - [`success()`](#success)
  - [`then()`](#then)
  - [`unwrap()`](#unwrap)
  - [`val()`](#val)

</details>

## Namespace

`OpenFGA\Results`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php)

## Implements

- [`ResultInterface`](ResultInterface.md)
- [`SuccessInterface`](SuccessInterface.md)

## Related Classes

- [SuccessInterface](Results/SuccessInterface.md) (interface)

## Methods

### err

```php
public function err(): never

```

Retrieves the error from a failed result. This method should only be called on Failure results. Use failed() to check the result type before calling this method to avoid exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L41)

#### Returns

`never` — The error that caused the failure

### failed

```php
public function failed(): bool

```

Determines if this result represents a failure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L50)

#### Returns

`bool` — True if this is a Failure result, false if it&#039;s a Success

### failure

```php
public function failure(callable $fn): OpenFGA\Results\ResultInterface

```

Executes a callback when the result is a failure and continues the chain. The callback receives the error as its parameter and is only executed for Failure results. This method always returns the original result unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L59)

#### Parameters

| Name  | Type       | Description |
| ----- | ---------- | ----------- |
| `$fn` | `callable` |             |

#### Returns

[`ResultInterface`](ResultInterface.md) — The original result for method chaining

### recover

```php
public function recover(callable $fn): OpenFGA\Results\ResultInterface

```

Recovers from a failure by transforming it into a success or different failure. The callback is only executed for Failure results and can return either a new Result or a plain value (which becomes a Success). Success results pass through unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L68)

#### Parameters

| Name  | Type       | Description |
| ----- | ---------- | ----------- |
| `$fn` | `callable` |             |

#### Returns

[`ResultInterface`](ResultInterface.md) — The recovered result or original success

### rethrow

```php
public function rethrow(?Throwable $throwable = NULL): OpenFGA\Results\ResultInterface

```

Throws the contained error or continues the chain. For Failure results, this throws either the provided throwable or the contained error. For Success results, this method has no effect and returns the result unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L77)

#### Parameters

| Name         | Type                      | Description                                                |
| ------------ | ------------------------- | ---------------------------------------------------------- |
| `$throwable` | `Throwable` &#124; `null` | Optional throwable to throw instead of the contained error |

#### Returns

[`ResultInterface`](ResultInterface.md) — The original result for method chaining

### succeeded

```php
public function succeeded(): bool

```

Determines if this result represents a success.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L86)

#### Returns

`bool` — True if this is a Success result, false if it&#039;s a Failure

### success

```php
public function success(callable $fn): OpenFGA\Results\ResultInterface

```

Executes a callback when the result is a success and continues the chain. The callback receives the success value (specific response interface) as its parameter and is only executed for Success results. This method always returns the original result unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L95)

#### Parameters

| Name  | Type       | Description |
| ----- | ---------- | ----------- |
| `$fn` | `callable` |             |

#### Returns

[`ResultInterface`](ResultInterface.md) — The original result for method chaining

### then

```php
public function then(callable $fn): OpenFGA\Results\ResultInterface

```

Transforms a successful result using a callback and continues the chain. The callback is only executed for Success results and receives the specific response interface as its parameter. It can return either a new Result or a plain value (which becomes a Success). Failure results pass through unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L108)

#### Parameters

| Name  | Type       | Description |
| ----- | ---------- | ----------- |
| `$fn` | `callable` |             |

#### Returns

[`ResultInterface`](ResultInterface.md) — The transformed result or original failure

### unwrap

```php
public function unwrap(?callable $fn = NULL): mixed

```

Extracts the value from the result or applies a transformation. Without a callback, this returns the success value (specific response interface) or throws the failure error. With a callback, the function is called with either the response interface or failure error, and its return value is returned instead of throwing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Result.php#L22)

#### Parameters

| Name  | Type                     | Description |
| ----- | ------------------------ | ----------- |
| `$fn` | `callable` &#124; `null` |             |

#### Returns

`mixed` — The response interface, callback result, or throws the error

### val

```php
public function val(): mixed

```

Retrieves the value from a successful result. This method should only be called on Success results. Use succeeded() to check the result type before calling this method to avoid exceptions. Returns the specific response interface documented in the calling method&#039;s @return annotation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Results/Success.php#L124)

#### Returns

`mixed` — The response interface (for example CheckResponseInterface, StoreInterface)
