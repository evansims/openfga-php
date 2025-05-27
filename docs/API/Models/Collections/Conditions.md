# Conditions


## Namespace
`OpenFGA\Models\Collections`

## Implements
* [IndexedCollectionInterface](Models/Collections/IndexedCollectionInterface.md)
* Traversable
* JsonSerializable
* Iterator
* Countable
* ArrayAccess
* [ConditionsInterface](Models/Collections/ConditionsInterface.md)



## Methods
### add


```php
public function add(mixed $item): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `callback` | callable |  |

#### Returns
static

### clear


```php
public function clear(): void
```



#### Returns
void

### count


```php
public function count(): int
```



#### Returns
int

### current


```php
public function current(): OpenFGA\Models\ModelInterface
```



#### Returns
[ModelInterface](Models/ModelInterface.md)

### every


```php
public function every(callable $callback): bool
```

Checks if all items match the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `callback` | callable |  |

#### Returns
bool

### filter


```php
public function filter(callable $callback): static
```

Filters the collection using a callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `callback` | ?callable |  |

#### Returns
static

### first


```php
public function first(?callable $callback = NULL)
```

Returns the first item that matches the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `offset` | int |  |


### get


```php
public function get(int $offset)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `offset` | mixed |  |


### isEmpty


```php
public function isEmpty(): bool
```



#### Returns
bool

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### key


```php
public function key(): int
```



#### Returns
int

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
| `offset` | mixed |  |

#### Returns
bool

### offsetGet


```php
public function offsetGet(mixed $offset): ?OpenFGA\Models\ModelInterface
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `value` | mixed |  |

#### Returns
?[ModelInterface](Models/ModelInterface.md)

### offsetSet


```php
public function offsetSet(mixed $offset, mixed $value): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |
| `offset` | mixed |  |

#### Returns
void

### offsetUnset


```php
public function offsetUnset(mixed $offset): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `callback` | callable |  |

#### Returns
void

### reduce


```php
public function reduce(mixed $initial, callable $callback): mixed
```

Reduces the collection to a single value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$initial` | mixed |  |
| `callback` | callable |  |

#### Returns
mixed

### rewind


```php
public function rewind(): void
```



#### Returns
void

### schema

*<small>Implements Models\Collections\IndexedCollectionInterface</small>*  

```php
public function schema(): OpenFGA\Schema\CollectionSchemaInterface
```



#### Returns
[CollectionSchemaInterface](Schema/CollectionSchemaInterface.md)

### some


```php
public function some(callable $callback): bool
```

Checks if any item matches the callback.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `items` |  |  |

#### Returns
bool

### toArray


```php
public function toArray(): array
```



#### Returns
array

### valid


```php
public function valid(): bool
```



#### Returns
bool

### withItems


```php
public function withItems(mixed $items): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$items` | mixed |  |

#### Returns
static

