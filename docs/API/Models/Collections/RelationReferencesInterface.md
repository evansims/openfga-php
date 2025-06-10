# RelationReferencesInterface

## Namespace

`OpenFGA\Models\Collections`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/RelationReferencesInterface.php)

## Implements

* [`IndexedCollectionInterface`](IndexedCollectionInterface.md)
* `Traversable`
* `JsonSerializable`
* `Iterator`
* `Countable`
* `ArrayAccess`

## Related Classes

* [RelationReferences](../Models/Collections/RelationReferences.md) (implementation)

## Methods

### List Operations

#### get

```php
public function get(int $offset): T|null

```

Get an item by its position in the collection. This method retrieves the item at the specified index position. Returns null if the index is out of bounds.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L161)

#### Parameters

| Name      | Type  | Description                                |
| --------- | ----- | ------------------------------------------ |
| `$offset` | `int` | The index position of the item to retrieve |

#### Returns

`T` &#124; `null` — The item at the specified position, or null if not found

#### offsetGet

```php
public function offsetGet(mixed $offset): T|null

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L217)

#### Parameters

| Name      | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | `mixed` |             |

#### Returns

`T` &#124; `null`

### Utility

#### isEmpty

```php
public function isEmpty(): bool

```

Check if the collection contains no items. This method provides a convenient way to test whether the collection is empty without needing to check the count.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L171)

#### Returns

`bool` — True if the collection is empty, false otherwise

#### offsetExists

```php
public function offsetExists(mixed $offset): bool

```

Check if an offset exists in the collection. This method determines whether the collection contains an item at the specified offset position.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L209)

#### Parameters

| Name      | Type    | Description                       |
| --------- | ------- | --------------------------------- |
| `$offset` | `mixed` | The offset to check for existence |

#### Returns

`bool` — True if the offset exists, false otherwise

#### offsetSet

```php
public function offsetSet(int|string|null $offset, T $value): void

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L226)

#### Parameters

| Name      | Type                                | Description |
| --------- | ----------------------------------- | ----------- |
| `$offset` | `int` &#124; `string` &#124; `null` |             |
| `$value`  | `T`                                 |             |

#### Returns

`void`

#### offsetUnset

```php
public function offsetUnset(mixed $offset): void

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L232)

#### Parameters

| Name      | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | `mixed` |             |

#### Returns

`void`

### Other

#### add

```php
public function add(T $item): static

```

Add an item to the end of the collection. This method appends a new model object to the collection, automatically assigning it the next available integer index. The item is validated to ensure it matches the expected type for this collection, maintaining type safety throughout the authorization data processing pipeline. This operation modifies the current collection instance directly, making it suitable for building collections incrementally. For immutable operations, use the `withItems()` method instead, which creates new collection instances without modifying the original.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L95)

#### Parameters

| Name    | Type | Description                                       |
| ------- | ---- | ------------------------------------------------- |
| `$item` | `T`  | The OpenFGA model object to add to the collection |

#### Returns

`static`

#### clear

```php
public function clear(): void

```

Remove all items from the collection. This method empties the collection, resetting it to its initial state with no items and a count of zero.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L103)

#### Returns

`void`

#### count

```php
public function count(): int<0, max>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L109)

#### Returns

`int&lt;`0`, `max`&gt;`

#### current

```php
public function current(): T

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L116)

#### Returns

`T`

#### every

```php
public function every(callable $callback): bool

```

Check if all items in the collection match the given condition. This method tests whether all items in the collection satisfy the provided callback function. Returns true if all items pass the test, false if any item fails.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L128)

#### Parameters

| Name        | Type       | Description |
| ----------- | ---------- | ----------- |
| `$callback` | `callable` |             |

#### Returns

`bool` — True if all items match the condition, false otherwise

#### filter

```php
public function filter(callable $callback): static

```

Create a new collection containing only items that match the condition. This method creates a new collection containing only the items from the current collection that satisfy the provided callback function.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L139)

#### Parameters

| Name        | Type       | Description |
| ----------- | ---------- | ----------- |
| `$callback` | `callable` |             |

#### Returns

`static` — A new collection containing only the matching items

#### first

```php
public function first(?callable $callback = NULL): T|null

```

Get the first item in the collection, optionally matching a condition. When called without a callback, returns the first item in the collection. When called with a callback, returns the first item that satisfies the condition.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L150)

#### Parameters

| Name        | Type                     | Description |
| ----------- | ------------------------ | ----------- |
| `$callback` | `callable` &#124; `null` |             |

#### Returns

`T` &#124; `null` — The first matching item, or null if none found

#### jsonSerialize

```php
public function jsonSerialize(): array<string, array{type: string, relation?: string, wildcard?: object, condition?: string}>

```

Serialize the collection to an array.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/RelationReferencesInterface.php#L20)

#### Returns

`array&lt;`string`, `array{type: string`, `relation?: string`, `wildcard?: object`, `condition?: string}`&gt;`

#### key

```php
public function key(): int

```

Get the current iterator key. This method returns the current position in the collection iteration, which is always an integer for indexed collections.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L188)

#### Returns

`int` — The current iterator position

#### next

```php
public function next(): void

```

Move the iterator to the next position. This method advances the internal iterator pointer to the next item in the collection sequence.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L197)

#### Returns

`void`

#### reduce

```php
public function reduce(U $initial, callable $callback): U

```

Reduce the collection to a single value using a callback function. This method iteratively applies a callback function to accumulate the collection items into a single value, starting with an initial value.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L246)

#### Parameters

| Name        | Type       | Description                              |
| ----------- | ---------- | ---------------------------------------- |
| `$initial`  | `U`        | The initial value to start the reduction |
| `$callback` | `callable` |                                          |

#### Returns

`U` — The final accumulated value

#### rewind

```php
public function rewind(): void

```

Reset the iterator to the beginning of the collection. This method moves the internal iterator pointer back to the first item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L255)

#### Returns

`void`

#### some

```php
public function some(callable $callback): bool

```

Check if at least one item in the collection matches the given condition. This method tests whether at least one item in the collection satisfies the provided callback function. Returns true if any item passes the test, false if all items fail.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L267)

#### Parameters

| Name        | Type       | Description |
| ----------- | ---------- | ----------- |
| `$callback` | `callable` |             |

#### Returns

`bool` — True if any item matches the condition, false otherwise

#### toArray

```php
public function toArray(): array<int|string, T>

```

Convert the collection to a standard PHP array. This method creates a native PHP array containing all items in the collection, preserving their order and indexes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L277)

#### Returns

`array&lt;int` &#124; `string, T&gt;` — A standard PHP array containing all collection items

#### valid

```php
public function valid(): bool

```

Check if the current iterator position is valid. This method determines whether the current iterator position points to a valid item in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L288)

#### Returns

`bool` — True if the current position is valid, false otherwise

#### withItems

```php
public function withItems(mixed $items): static

```

Create a new collection with the specified items. This method creates a fresh collection instance containing only the provided items, leaving the original collection unchanged.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/IndexedCollectionInterface.php#L302)

#### Parameters

| Name     | Type  | Description |
| -------- | ----- | ----------- |
| `$items` | mixed |             |

#### Returns

`static` — A new collection instance containing the specified items
