# KeyedCollectionInterface

Represents a collection that is indexed by a string, like a JSON object.

## Namespace
`OpenFGA\Models\Collections`

## Implements
* ArrayAccess
* Countable
* Iterator
* JsonSerializable
* Traversable



## Methods
### add


```php
public function add(string $key, [ModelInterface](Models/ModelInterface.md) $item): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` |  |
| `$item` | `[ModelInterface](Models/ModelInterface.md)` |  |

#### Returns
`static`

### count


```php
public function count(): int
```



#### Returns
`int`

### current


```php
public function current(): [ModelInterface](Models/ModelInterface.md)
```



#### Returns
`[ModelInterface](Models/ModelInterface.md)`

### get


```php
public function get(string $key)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` |  |


### has


```php
public function has(string $key): bool
```

Check if a key exists in the collection.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` |  |

#### Returns
`bool`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 mixed&gt;

### key


```php
public function key(): string
```



#### Returns
`string`

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
public function offsetGet(mixed $offset)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |


### offsetSet


```php
public function offsetSet(mixed $offset, mixed $value): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |
| `$value` | `mixed` |  |

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

### rewind


```php
public function rewind(): void
```



#### Returns
`void`

### toArray


```php
public function toArray(): array
```



#### Returns
`array`
 T&gt;

### valid


```php
public function valid(): bool
```



#### Returns
`bool`

