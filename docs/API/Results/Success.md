# Success


## Namespace
`OpenFGA\Results`

## Implements
* [ResultInterface](Results/ResultInterface.md)



## Methods
### fold


```php
public function fold(callable $onSuccess, callable $onFailure): mixed
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$onSuccess` | `callable` |  |
| `fn` | `callable` |  |

#### Returns
`mixed`

### getError


```php
public function getError(): never
```



#### Returns
`never`

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
public function map(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 never&gt;

### mapError


```php
public function mapError(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 E&gt;

### onFailure


```php
public function onFailure(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 E&gt;

### onSuccess


```php
public function onSuccess(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 E&gt;

### tap


```php
public function tap(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `fn` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 E&gt;

### tapError


```php
public function tapError(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `default` | `mixed` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
 E&gt;

### then


```php
public function then(callable $fn): [ResultInterface](Results/ResultInterface.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `onFailure` | `callable` |  |

#### Returns
`[ResultInterface](Results/ResultInterface.md)`
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

