# CollectionSchema


## Namespace
`OpenFGA\Schema`

## Implements
* `OpenFGA\Schema\CollectionSchemaInterface`
* `OpenFGA\Schema\SchemaInterface`

## Methods
### getClassName

```php
public function getClassName(): string
```



#### Returns
`string` 

### getItemType

```php
public function getItemType(): string
```



#### Returns
`string` 

### getProperties

```php
public function getProperties(): array
```



#### Returns
`array` 

### getProperty

```php
public function getProperty(string $name): ?[SchemaProperty](Schema/SchemaProperty.md)
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | `string` |  |

#### Returns
`?[SchemaProperty](Schema/SchemaProperty.md)` 

### requiresItems

```php
public function requiresItems(): bool
```



#### Returns
`bool` 

