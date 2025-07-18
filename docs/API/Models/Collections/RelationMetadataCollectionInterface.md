# RelationMetadataCollectionInterface

Collection interface for OpenFGA relation metadata objects. This interface defines a keyed collection that holds relation metadata objects, which provide additional information about the relations defined in authorization model type definitions. Relation metadata includes details such as the module name and source file information for authorization models. The collection is keyed by relation names, allowing efficient access to metadata for specific relations within a type definition.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`add()`](#add)
  - [`count()`](#count)
  - [`current()`](#current)
  - [`get()`](#get)
  - [`has()`](#has)
  - [`isEmpty()`](#isempty)
  - [`jsonSerialize()`](#jsonserialize)
  - [`key()`](#key)
  - [`next()`](#next)
  - [`offsetExists()`](#offsetexists)
  - [`offsetGet()`](#offsetget)
  - [`offsetSet()`](#offsetset)
  - [`offsetUnset()`](#offsetunset)
  - [`rewind()`](#rewind)
  - [`toArray()`](#toarray)
  - [`valid()`](#valid)

</details>

## Namespace

`OpenFGA\Models\Collections`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/RelationMetadataCollectionInterface.php)

## Implements

- [`KeyedCollectionInterface`](KeyedCollectionInterface.md)
- `Traversable`
- `JsonSerializable`
- `Iterator`
- `Countable`
- `ArrayAccess`

## Related Classes

- [RelationMetadataCollection](../Models/Collections/RelationMetadataCollection.md) (implementation)
- [RelationMetadataInterface](../Models/RelationMetadataInterface.md) (item)

## Methods

### add

```php
public function add(string $key, T $item): static

```

Add an item to the collection with the specified key. This method associates an item with a string key, allowing for named access to collection elements similar to an associative array.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L52)

#### Parameters

| Name    | Type     | Description                               |
| ------- | -------- | ----------------------------------------- |
| `$key`  | `string` | The string key to associate with the item |
| `$item` | `T`      | The item to add to the collection         |

#### Returns

`static`

### count

```php
public function count(): int<0, max>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L58)

#### Returns

`int&lt;`0`, `max`&gt;`

### current

```php
public function current(): T

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L67)

#### Returns

`T`

### get

```php
public function get(string $key): T|null

```

Get an item by its string key. This method retrieves the item associated with the specified key. Returns null if no item is found with the given key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L78)

#### Parameters

| Name   | Type     | Description                     |
| ------ | -------- | ------------------------------- |
| `$key` | `string` | The key of the item to retrieve |

#### Returns

`T` &#124; `null` — The item associated with the key, or null if not found

### has

```php
public function has(string $key): bool

```

Check if a key exists in the collection. This method determines whether the collection contains an item associated with the specified key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L89)

#### Parameters

| Name   | Type     | Description                    |
| ------ | -------- | ------------------------------ |
| `$key` | `string` | The key to check for existence |

#### Returns

`bool` — True if the key exists, false otherwise

### isEmpty

```php
public function isEmpty(): bool

```

Check if the collection contains no items. This method provides a convenient way to test whether the collection is empty without needing to check the count.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L99)

#### Returns

`bool` — True if the collection is empty, false otherwise

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L105)

#### Returns

`array&lt;`string`, `mixed`&gt;`

### key

```php
public function key(): string

```

Get the current iterator key. This method returns the current string key in the collection iteration. For keyed collections, this is always a string identifier.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L118)

#### Returns

`string` — The current iterator key

### next

```php
public function next(): void

```

Move the iterator to the next position. This method advances the internal iterator pointer to the next key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L127)

#### Returns

`void`

### offsetExists

```php
public function offsetExists(mixed $offset): bool

```

Check if an offset exists in the collection. This method determines whether the collection contains an item with the specified key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L139)

#### Parameters

| Name      | Type    | Description                    |
| --------- | ------- | ------------------------------ |
| `$offset` | `mixed` | The key to check for existence |

#### Returns

`bool` — True if the key exists, false otherwise

### offsetGet

```php
public function offsetGet(mixed $offset): T|null

```

Get an item by its offset key.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L149)

#### Parameters

| Name      | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | `mixed` |             |

#### Returns

`T` &#124; `null`

### offsetSet

```php
public function offsetSet(string|null $offset, T $value): void

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L158)

#### Parameters

| Name      | Type                   | Description |
| --------- | ---------------------- | ----------- |
| `$offset` | `string` &#124; `null` |             |
| `$value`  | `T`                    |             |

#### Returns

`void`

### offsetUnset

```php
public function offsetUnset(mixed $offset): void

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L164)

#### Parameters

| Name      | Type    | Description |
| --------- | ------- | ----------- |
| `$offset` | `mixed` |             |

#### Returns

`void`

### rewind

```php
public function rewind(): void

```

Reset the iterator to the beginning of the collection. This method moves the internal iterator pointer back to the first key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L173)

#### Returns

`void`

### toArray

```php
public function toArray(): array<string, T>

```

Convert the collection to a standard PHP associative array. This method creates a native PHP associative array containing all items in the collection, preserving their string keys and values.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L183)

#### Returns

`array&lt;`string`, `T`&gt;` — An associative array containing all collection items

### valid

```php
public function valid(): bool

```

Check if the current iterator position is valid. This method determines whether the current iterator position points to a valid key-value pair in the collection.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Collections/KeyedCollectionInterface.php#L194)

#### Returns

`bool` — True if the current position is valid, false otherwise
