# ComputedsInterface


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

Add an item to the end of the collection. This method appends a new model object to the collection, automatically assigning it the next available integer index. The item is validated to ensure it matches the expected type for this collection, maintaining type safety throughout the authorization data processing pipeline. This operation modifies the current collection instance directly, making it suitable for building collections incrementally. For immutable operations, use the `withItems()` method instead, which creates new collection instances without modifying the original.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$item` | T | The OpenFGA model object to add to the collection |

#### Returns
static

### clear


```php
public function clear(): void
```

Remove all items from the collection. This method empties the collection, resetting it to its initial state with no items and a count of zero.


#### Returns
void

### count


```php
public function count(): int<0, max>
```



#### Returns
int&lt;0, max&gt;

### current


```php
public function current(): T
```



#### Returns
T

### every


```php
public function every(callable $callback): bool
```

Check if all items in the collection match the given condition. This method tests whether all items in the collection satisfy the provided callback function. Returns true if all items pass the test, false if any item fails.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
bool
 True if all items match the condition, false otherwise

### filter


```php
public function filter(callable $callback): static<T>
```

Create a new collection containing only items that match the condition. This method creates a new collection containing only the items from the current collection that satisfy the provided callback function.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
static&lt;T&gt;
 A new collection containing only the matching items

### first


```php
public function first(?callable $callback = NULL): T|null
```

Get the first item in the collection, optionally matching a condition. When called without a callback, returns the first item in the collection. When called with a callback, returns the first item that satisfies the condition.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | ?callable |  |

#### Returns
T | null
 The first matching item, or null if none found

### get


```php
public function get(int $offset): T|null
```

Get an item by its position in the collection. This method retrieves the item at the specified index position. Returns null if the index is out of bounds.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | int | The index position of the item to retrieve |

#### Returns
T | null
 The item at the specified position, or null if not found

### isEmpty


```php
public function isEmpty(): bool
```

Check if the collection contains no items. This method provides a convenient way to test whether the collection is empty without needing to check the count.


#### Returns
bool
 True if the collection is empty, false otherwise

### jsonSerialize


```php
public function jsonSerialize(): array<int, array{userset: string}>
```



#### Returns
array&lt;int, array{userset: string}&gt;

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
| `$offset` | mixed |  |

#### Returns
bool

### offsetGet


```php
public function offsetGet(mixed $offset): T|null
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |

#### Returns
T | null

### offsetSet


```php
public function offsetSet(int|string|null $offset, T $value): void
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | int | string | null |  |
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

### reduce


```php
public function reduce(U $initial, callable $callback): U
```

Reduce the collection to a single value using a callback function. This method iteratively applies a callback function to accumulate the collection items into a single value, starting with an initial value.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$initial` | U | The initial value to start the reduction |
| `$callback` | callable |  |

#### Returns
U
 The final accumulated value

### rewind


```php
public function rewind(): void
```



#### Returns
void

### some


```php
public function some(callable $callback): bool
```

Check if at least one item in the collection matches the given condition. This method tests whether at least one item in the collection satisfies the provided callback function. Returns true if any item passes the test, false if all items fail.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
bool
 True if any item matches the condition, false otherwise

### toArray


```php
public function toArray(): array<int|string, T>
```

Convert the collection to a standard PHP array. This method creates a native PHP array containing all items in the collection, preserving their order and indexes.


#### Returns
array&lt;int | string, T&gt;
 A standard PHP array containing all collection items

### valid


```php
public function valid(): bool
```



#### Returns
bool

### withItems


```php
public function withItems(mixed $items): static<T>
```

Create a new collection with the specified items. This method creates a fresh collection instance containing only the provided items, leaving the original collection unchanged.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$items` | mixed |  |

#### Returns
static&lt;T&gt;
 A new collection instance containing the specified items

