# TypeDefinitionRelations


## Namespace
`OpenFGA\Models\Collections`

## Implements
* [KeyedCollectionInterface](Models/Collections/KeyedCollectionInterface.md)
* Traversable
* JsonSerializable
* Iterator
* Countable
* ArrayAccess
* [TypeDefinitionRelationsInterface](Models/Collections/TypeDefinitionRelationsInterface.md)



## Methods
### add


```php
public function add(string $key, OpenFGA\Models\ModelInterface $item): static
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$key` | string |  |
| `key` | string |  |

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
public function current(): OpenFGA\Models\ModelInterface
```



#### Returns
[ModelInterface](Models/ModelInterface.md)

### get


```php
public function get(string $key)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `key` | string |  |


### has


```php
public function has(string $key): bool
```

Check if a key exists in the collection.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `offset` | mixed |  |

#### Returns
bool

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
| `offset` | mixed |  |

#### Returns
bool

### offsetGet


```php
public function offsetGet(mixed $offset)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `value` | mixed |  |


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



#### Returns
void

### schema

*<small>Implements Models\Collections\KeyedCollectionInterface</small>*  

```php
public function schema(): OpenFGA\Schema\CollectionSchemaInterface
```



#### Returns
[CollectionSchemaInterface](Schema/CollectionSchemaInterface.md)

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

