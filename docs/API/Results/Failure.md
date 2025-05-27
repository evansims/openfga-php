# Failure


## Namespace
`OpenFGA\Results`

## Implements
* [ResultInterface](Results/ResultInterface.md)



## Methods
### err


```php
public function err(): E
```

Return the unwrapped error of a `Failure`.


#### Returns
E

### failed


```php
public function failed(): bool
```

Return `true` if this is a `Failure`.


#### Returns
bool

### failure


```php
public function failure(callable $fn): OpenFGA\Results\ResultInterface
```

Execute on `Failure` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)

### recover


```php
public function recover(callable $fn): OpenFGA\Results\ResultInterface
```

Execute on `Failure`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `throwable` | ?Throwable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)

### rethrow


```php
public function rethrow(?Throwable $throwable = NULL): OpenFGA\Results\ResultInterface
```

Throw the error of a `Failure`, or continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)

### succeeded


```php
public function succeeded(): bool
```

Return `true` if this is a `Success`.


#### Returns
bool

### success


```php
public function success(callable $fn): OpenFGA\Results\ResultInterface
```

Execute on `Success` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | callable |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)

### then


```php
public function then(callable $fn): OpenFGA\Results\ResultInterface
```

Execute on `Success`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `default` | mixed |  |

#### Returns
[ResultInterface](Results/ResultInterface.md)

### unwrap


```php
public function unwrap(mixed $default = NULL): mixed
```

Return the unwrapped value of a `Success`, or a default value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$default` | mixed |  |

#### Returns
mixed

### val


```php
public function val(): never
```

Return the unwrapped value of a `Success`.


#### Returns
never

