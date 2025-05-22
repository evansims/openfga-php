# ResultInterface


## Namespace
`OpenFGA\Results`




## Methods
### fold


```php
public function fold(callable $onSuccess, callable $onFailure): mixed
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$onSuccess` | `callable` |  |
| `$onFailure` | `callable` |  |

#### Returns
`mixed`

### getError


```php
public function getError(): Throwable
```



#### Returns
`Throwable`

### getValue


```php
public function getValue(): mixed
```



#### Returns
`mixed`

### isFailure


```php
public function isFailure(): bool
```



#### Returns
`bool`

### isSuccess


```php
public function isSuccess(): bool
```



#### Returns
`bool`

### map


```php
public function map(callable $fn): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### mapError


```php
public function mapError(callable $fn): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 F&gt;

### onFailure


```php
public function onFailure(callable $fn): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### onSuccess


```php
public function onSuccess(callable $fn): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### tap


```php
public function tap(callable $fn): self
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### tapError


```php
public function tapError(callable $fn): self
```


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


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$fn` | `callable` |  |

#### Returns
`self`
 E&gt;

### unwrap


```php
public function unwrap(mixed $default = NULL): mixed
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$default` | `mixed` |  |

#### Returns
`mixed`

