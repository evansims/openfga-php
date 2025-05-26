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
`Throwable`

### failed


```php
public function failed(): bool
```

Return `true` if this is a `Failure`.


#### Returns
`bool`

### failure


```php
public function failure(callable $fn): self
```

Execute on `Failure` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### recover


```php
public function recover(callable $fn): self
```

Execute on `Failure`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 F&gt;

### rethrow


```php
public function rethrow(?Throwable $throwable = NULL): self
```

Throw the error of a `Failure`, or continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$throwable` | `?Throwable` |  |

#### Returns
`self`
 E&gt;

### succeeded


```php
public function succeeded(): bool
```

Return `true` if this is a `Success`.


#### Returns
`bool`

### success


```php
public function success(callable $fn): self
```

Execute on `Success` and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### then


```php
public function then(callable $fn): self
```

Execute on `Success`, mutate the result, and continue the chain.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 F&gt;

### unwrap


```php
public function unwrap(mixed $default = NULL): mixed
```

Return the unwrapped value of a `Success`, or a default value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$default` | `mixed` |  |

#### Returns
`mixed`

### val


```php
public function val(): mixed
```

Return the unwrapped value of a `Success`.


#### Returns
`mixed`

