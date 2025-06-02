# BatchCheckItems

Collection of batch check items for batch authorization requests. This collection maintains a list of BatchCheckItem objects, each representing a single authorization check to be performed as part of a batch request.

## Namespace
`OpenFGA\Models\Collections`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/BatchCheckItems.php)

## Implements
* [IndexedCollectionInterface](IndexedCollectionInterface.md)
* Traversable
* JsonSerializable
* Iterator
* Countable
* ArrayAccess
* [BatchCheckItemsInterface](BatchCheckItemsInterface.md)

## Related Classes
* [BatchCheckItemsInterface](../Models/Collections/BatchCheckItemsInterface.md) (interface)



## Methods

                                                                                                                                                                                                                                                                                                                                    
### List Operations
#### get


```php
public function get(int $offset)
```

Get an item by its position in the collection. This method retrieves the item at the specified index position. Returns null if the index is out of bounds.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L247)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | int | The index position of the item to retrieve |


#### offsetGet


```php
public function offsetGet(mixed $offset): ?OpenFGA\Models\ModelInterface
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L318)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |

#### Returns
[ModelInterface](../Models/ModelInterface.md) &#124; null

### Model Management
#### schema

*<small>Implements Models\Collections\IndexedCollectionInterface</small>*  

```php
public function schema(): CollectionSchemaInterface
```

Get the schema definition for this collection type. Returns the schema that defines the structure, validation rules, and serialization behavior for this collection type. The schema specifies the expected item type, validation constraints, and transformation rules that ensure all items in the collection conform to OpenFGA data requirements. Collection schemas enable: - Type validation for all added items - Consistent serialization across different contexts - API compatibility verification - Runtime type checking and error reporting The schema system ensures that authorization data maintains integrity throughout processing, preventing type mismatches that could lead to authorization failures or security vulnerabilities.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L74)


#### Returns
CollectionSchemaInterface
 The schema definition containing validation rules and type constraints for this collection

### Utility
#### isEmpty


```php
public function isEmpty(): bool
```

Check if the collection contains no items. This method provides a convenient way to test whether the collection is empty without needing to check the count.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L256)


#### Returns
bool
 True if the collection is empty, false otherwise

#### offsetExists


```php
public function offsetExists(mixed $offset): bool
```

Check if an offset exists in the collection. This method determines whether the collection contains an item at the specified offset position.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L305)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed | The offset to check for existence |

#### Returns
bool
 True if the offset exists, false otherwise

#### offsetSet


```php
public function offsetSet(mixed $offset, mixed $value): void
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L335)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |
| `$value` | mixed |  |

#### Returns
void

#### offsetUnset


```php
public function offsetUnset(mixed $offset): void
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L352)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | mixed |  |

#### Returns
void

### Other
#### add


```php
public function add(mixed $item): static
```

Add an item to the end of the collection. This method appends a new model object to the collection, automatically assigning it the next available integer index. The item is validated to ensure it matches the expected type for this collection, maintaining type safety throughout the authorization data processing pipeline. This operation modifies the current collection instance directly, making it suitable for building collections incrementally. For immutable operations, use the `withItems()` method instead, which creates new collection instances without modifying the original.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L138)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$item` | mixed | The OpenFGA model object to add to the collection |

#### Returns
static

#### clear


```php
public function clear(): void
```

Remove all items from the collection. This method empties the collection, resetting it to its initial state with no items and a count of zero.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L153)


#### Returns
void

#### count


```php
public function count(): int<0, max>
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L165)


#### Returns
int&lt;0, max&gt;

#### current


```php
public function current(): OpenFGA\Models\ModelInterface
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L178)


#### Returns
[ModelInterface](../Models/ModelInterface.md)

#### every


```php
public function every(callable $callback): bool
```

Check if all items in the collection match the given condition. This method tests whether all items in the collection satisfy the provided callback function. Returns true if all items pass the test, false if any item fails.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L189)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
bool
 True if all items match the condition, false otherwise

#### filter


```php
public function filter(callable $callback): static
```

Create a new collection containing only items that match the condition. This method creates a new collection containing only the items from the current collection that satisfy the provided callback function.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L210)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
static
 A new collection containing only the matching items

#### first


```php
public function first(?callable $callback = NULL)
```

Get the first item in the collection, optionally matching a condition. When called without a callback, returns the first item in the collection. When called with a callback, returns the first item that satisfies the condition.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L228)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable &#124; null |  |


#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L265)


#### Returns
array

#### key


```php
public function key(): int
```

Get the current iterator key. This method returns the current position in the collection iteration, which is always an integer for indexed collections.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L281)


#### Returns
int
 The current iterator position

#### next


```php
public function next(): void
```

Move the iterator to the next position. This method advances the internal iterator pointer to the next item in the collection sequence.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L296)


#### Returns
void

#### reduce


```php
public function reduce(mixed $initial, callable $callback): mixed
```

Reduce the collection to a single value using a callback function. This method iteratively applies a callback function to accumulate the collection items into a single value, starting with an initial value.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L368)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$initial` | mixed | The initial value to start the reduction |
| `$callback` | callable |  |

#### Returns
mixed
 The final accumulated value

#### rewind


```php
public function rewind(): void
```

Reset the iterator to the beginning of the collection. This method moves the internal iterator pointer back to the first item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L382)


#### Returns
void

#### some


```php
public function some(callable $callback): bool
```

Check if at least one item in the collection matches the given condition. This method tests whether at least one item in the collection satisfies the provided callback function. Returns true if any item passes the test, false if all items fail.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L391)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$callback` | callable |  |

#### Returns
bool
 True if any item matches the condition, false otherwise

#### toArray


```php
public function toArray(): array
```

Convert the collection to a standard PHP array. This method creates a native PHP array containing all items in the collection, preserving their order and indexes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L406)


#### Returns
array
 A standard PHP array containing all collection items

#### valid


```php
public function valid(): bool
```

Check if the current iterator position is valid. This method determines whether the current iterator position points to a valid item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L415)


#### Returns
bool
 True if the current position is valid, false otherwise

#### withItems


```php
public function withItems(mixed $items): static
```

Create a new collection with the specified items. This method creates a fresh collection instance containing only the provided items, leaving the original collection unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollection.php#L430)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$items` | mixed |  |

#### Returns
static
 A new collection instance containing the specified items

