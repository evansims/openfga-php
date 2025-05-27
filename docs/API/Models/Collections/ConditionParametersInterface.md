# ConditionParametersInterface


## Namespace
`OpenFGA\Models\Collections`

## Implements
* [IndexedCollectionInterface](Models/Collections/IndexedCollectionInterface.md)
* Traversable
* JsonSerializable
* Iterator
* Countable
* ArrayAccess



## Methods
### add


```php
public function add(T $item): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$item` | `T` |  |

#### Returns
`static`

### clear


```php
public function clear(): void
```



#### Returns
`void`

### count


```php
public function count(): int
```



#### Returns
`int`

### current


```php
public function current(): T
```



#### Returns
`T`

### every


```php
public function every(callable $callback): bool
```

Checks if all items match the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | `callable` |  |

#### Returns
`bool`

### filter


```php
public function filter(callable $callback): static<T>
```

Filters the collection using a callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | `callable` |  |

#### Returns
`static<T>`

### first


```php
public function first(?callable $callback = NULL): null | T
```

Returns the first item that matches the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | `?callable` |  |

#### Returns
`null | T`

### get


```php
public function get(int $offset): null | T
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `int` |  |

#### Returns
`null | T`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

### key


```php
public function key(): int
```



#### Returns
`int`

### next


```php
public function next(): void
```



#### Returns
`void`

### offsetExists


```php
public function offsetExists(mixed $offset): bool
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |

#### Returns
`bool`

### offsetGet


```php
public function offsetGet(mixed $offset): null | T
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |

#### Returns
`null | T`

### offsetSet


```php
public function offsetSet(null | int | string $offset, T $value): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `null | int | string` |  |
| `$value` | `T` |  |

#### Returns
`void`

### offsetUnset


```php
public function offsetUnset(mixed $offset): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |

#### Returns
`void`

### reduce


```php
public function reduce(U $initial, callable $callback): U
```

Reduces the collection to a single value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$initial` | `U` |  |
| `$callback` | `callable` |  |

#### Returns
`U`

### rewind


```php
public function rewind(): void
```



#### Returns
`void`

### some


```php
public function some(callable $callback): bool
```

Checks if any item matches the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | `callable` |  |

#### Returns
`bool`

### toArray


```php
public function toArray(): array<int | string, T>
```



#### Returns
`array<int | string, T>`

### valid


```php
public function valid(): bool
```



#### Returns
`bool`

### withItems


```php
public function withItems(mixed $items): static<T>
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$items` | `mixed` |  |

#### Returns
`static<T>`

