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
public function add(string $key, T $item): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | string |  |
| `$item` | T |  |

#### Returns
static

### count


```php
public function count(): int
```



#### Returns
int

### current


```php
public function current(): T
```



#### Returns
T

### get


```php
public function get(string $key): null|T
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | string |  |

#### Returns
null | T

### has


```php
public function has(string $key): bool
```

Check if a key exists in the collection.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | string |  |

#### Returns
bool

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```



#### Returns
array&lt;string, mixed&gt;

### key


```php
public function key(): string
```



#### Returns
string

### next


```php
public function next(): void
```



#### Returns
void

### offsetExists


```php
public function offsetExists(mixed $offset): bool
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |

#### Returns
bool

### offsetGet


```php
public function offsetGet(mixed $offset)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |


### offsetSet


```php
public function offsetSet(null|string $offset, T $value): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | null | string |  |
| `$value` | T |  |

#### Returns
void

### offsetUnset


```php
public function offsetUnset(mixed $offset): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |

#### Returns
void

### rewind


```php
public function rewind(): void
```



#### Returns
void

### toArray


```php
public function toArray(): array<string, T>
```



#### Returns
array&lt;string, T&gt;

### valid


```php
public function valid(): bool
```



#### Returns
bool

