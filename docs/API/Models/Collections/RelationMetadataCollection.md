# RelationMetadataCollection

Collection implementation for OpenFGA relation metadata objects. This class provides a concrete implementation for managing keyed collections of relation metadata objects. Relation metadata provides additional context about relations defined in authorization model type definitions, including module information and source file details. The collection uses relation names as keys, enabling efficient retrieval of metadata for specific relations within a type definition.

## Namespace
`OpenFGA\Models\Collections`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/RelationMetadataCollection.php)

## Implements
* [`KeyedCollectionInterface`](KeyedCollectionInterface.md)
* `Traversable`
* `JsonSerializable`
* `Iterator`
* `Countable`
* `ArrayAccess`
* [`RelationMetadataCollectionInterface`](RelationMetadataCollectionInterface.md)

## Related Classes
* [RelationMetadata](../Models/RelationMetadata.md) (item)
* [RelationMetadataCollectionInterface](../Models/Collections/RelationMetadataCollectionInterface.md) (interface)

## Methods

### List Operations
#### get

```php
public function get(string $key)
```

Get an item by its string key. This method retrieves the item associated with the specified key. Returns null if no item is found with the given key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L177)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` | The key of the item to retrieve |

#### offsetGet

```php
public function offsetGet(mixed $offset): ?OpenFGA\Models\ModelInterface
```

Get an item by its offset key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L267)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |

#### Returns
[`ModelInterface`](../Models/ModelInterface.md) &#124; `null`
### Model Management
#### schema

*<small>Implements Models\Collections\KeyedCollectionInterface</small>*

```php
public function schema(): CollectionSchemaInterface
```

Get the schema definition for this collection type. Returns the schema that defines the structure and validation rules for this collection, including the expected item type and constraints.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L37)

#### Returns
`CollectionSchemaInterface` — The collection schema
### Utility
#### has

```php
public function has(string $key): bool
```

Check if a key exists in the collection. This method determines whether the collection contains an item associated with the specified key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L186)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` | The key to check for existence |

#### Returns
`bool` — True if the key exists, false otherwise
#### isEmpty

```php
public function isEmpty(): bool
```

Check if the collection contains no items. This method provides a convenient way to test whether the collection is empty without needing to check the count.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L195)

#### Returns
`bool` — True if the collection is empty, false otherwise
#### offsetExists

```php
public function offsetExists(mixed $offset): bool
```

Check if an offset exists in the collection. This method determines whether the collection contains an item with the specified key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L254)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` | The key to check for existence |

#### Returns
`bool` — True if the key exists, false otherwise
#### offsetSet

```php
public function offsetSet(mixed $offset, mixed $value): void
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L284)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |
| `$value` | `mixed` |  |

#### Returns
`void`
#### offsetUnset

```php
public function offsetUnset(mixed $offset): void
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L301)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$offset` | `mixed` |  |

#### Returns
`void`
### Other
#### add

```php
public function add(string $key, OpenFGA\Models\ModelInterface $item): static
```

Add an item to the collection with the specified key. This method associates an item with a string key, allowing for named access to collection elements similar to an associative array.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L135)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | `string` | The string key to associate with the item |
| `$item` | [`ModelInterface`](../Models/ModelInterface.md) | The item to add to the collection |

#### Returns
`static`
#### count

```php
public function count(): int<0, max>
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L152)

#### Returns
`int&lt;`0`, `max`&gt;`
#### current

```php
public function current(): OpenFGA\Models\ModelInterface
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L166)

#### Returns
[`ModelInterface`](../Models/ModelInterface.md)
#### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L206)

#### Returns
`array&lt;`string`, `mixed`&gt;`
#### key

```php
public function key(): string
```

Get the current iterator key. This method returns the current string key in the collection iteration. For keyed collections, this is always a string identifier.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L230)

#### Returns
`string` — The current iterator key
#### next

```php
public function next(): void
```

Move the iterator to the next position. This method advances the internal iterator pointer to the next key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L245)

#### Returns
`void`
#### rewind

```php
public function rewind(): void
```

Reset the iterator to the beginning of the collection. This method moves the internal iterator pointer back to the first key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L312)

#### Returns
`void`
#### toArray

```php
public function toArray(): array
```

Convert the collection to a standard PHP associative array. This method creates a native PHP associative array containing all items in the collection, preserving their string keys and values.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L321)

#### Returns
`array` — An associative array containing all collection items
#### valid

```php
public function valid(): bool
```

Check if the current iterator position is valid. This method determines whether the current iterator position points to a valid key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollection.php#L340)

#### Returns
`bool` — True if the current position is valid, false otherwise
