# TypeDefinitionRelations


## Namespace
`OpenFGA\Models\Collections`

## Implements
* [KeyedCollectionInterface](KeyedCollectionInterface.md)
* Traversable
* JsonSerializable
* Iterator
* Countable
* ArrayAccess
* [TypeDefinitionRelationsInterface](TypeDefinitionRelationsInterface.md)



## Methods
### add


```php
public function add(string $key, OpenFGA\Models\ModelInterface $item): static
```

Add an item to the collection with the specified key. This method associates an item with a string key, allowing for named access to collection elements similar to an associative array.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | string | The string key to associate with the item |
| `key` | string |  |

#### Returns
static

### count


```php
public function count(): int<0, max>
```



#### Returns
int&lt;0, max&gt;

### current


```php
public function current(): OpenFGA\Models\ModelInterface
```



#### Returns
OpenFGA\Models\ModelInterface

### get


```php
public function get(string $key)
```

Get an item by its string key. This method retrieves the item associated with the specified key. Returns null if no item is found with the given key.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `key` | string |  |


### has


```php
public function has(string $key): bool
```

Check if a key exists in the collection. This method determines whether the collection contains an item associated with the specified key.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `offset` | mixed |  |

#### Returns
bool
 True if the key exists, false otherwise

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
public function jsonSerialize(): array<string, mixed>
```



#### Returns
array&lt;string, mixed&gt;

### key


```php
public function key(): string
```

Get the current iterator key. This method returns the current string key in the collection iteration. For keyed collections, this is always a string identifier.


#### Returns
string
 The current iterator key

### next


```php
public function next(): void
```

Move the iterator to the next position. This method advances the internal iterator pointer to the next key-value pair in the collection.


#### Returns
void

### offsetExists


```php
public function offsetExists(mixed $offset): bool
```

Check if an offset exists in the collection. This method determines whether the collection contains an item with the specified key.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `offset` | mixed |  |

#### Returns
bool
 True if the key exists, false otherwise

### offsetGet


```php
public function offsetGet(mixed $offset): ?OpenFGA\Models\ModelInterface
```

Get an item by its offset key.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `value` | mixed |  |

#### Returns
?OpenFGA\Models\ModelInterface

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
| `$offset` | mixed |  |

#### Returns
void

### rewind


```php
public function rewind(): void
```

Reset the iterator to the beginning of the collection. This method moves the internal iterator pointer back to the first key-value pair in the collection.


#### Returns
void

### schema

*<small>Implements Models\Collections\KeyedCollectionInterface</small>*  

```php
public function schema(): CollectionSchemaInterface
```

Get the schema definition for this collection type. Returns the schema that defines the structure and validation rules for this collection, including the expected item type and constraints.


#### Returns
CollectionSchemaInterface
 The collection schema

### toArray


```php
public function toArray(): array
```

Convert the collection to a standard PHP associative array. This method creates a native PHP associative array containing all items in the collection, preserving their string keys and values.


#### Returns
array
 An associative array containing all collection items

### valid


```php
public function valid(): bool
```

Check if the current iterator position is valid. This method determines whether the current iterator position points to a valid key-value pair in the collection.


#### Returns
bool
 True if the current position is valid, false otherwise

