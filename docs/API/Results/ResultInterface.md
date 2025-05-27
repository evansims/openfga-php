# ResultInterface


## Namespace
`OpenFGA\Results`




## Methods
### err


```php
public function err(): Throwable
```

Return the unwrapped error of a `Failure`.


#### Returns
Throwable

### failed


```php
public function failed(): bool
```

Return `true` if this is a `Failure`.


#### Returns
bool

### failure


```php
public function failure(callable $fn): ResultInterface<T, E>
```

Execute on `Failure` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;

### recover


```php
public function recover(callable $fn): ResultInterface<U, F>
```

Execute on `Failure`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;U, F&gt;

### rethrow


```php
public function rethrow(?Throwable $throwable = NULL): ResultInterface<T, E>
```

Throw the error of a `Failure`, or continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$throwable` | ?Throwable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;

### succeeded


```php
public function succeeded(): bool
```

Return `true` if this is a `Success`.


#### Returns
bool

### success


```php
public function success(callable $fn): ResultInterface<T, E>
```

Execute on `Success` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;T, E&gt;

### then


```php
public function then(callable $fn): ResultInterface<U, F>
```

Execute on `Success`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)&lt;U, F&gt;

### unwrap


```php
public function unwrap(R $default = NULL): R|T
```

Return the unwrapped value of a `Success`, or a default value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$default` | R |  |

#### Returns
R | T

### val


```php
public function val(): mixed
```

Return the unwrapped value of a `Success`.


#### Returns
mixed

