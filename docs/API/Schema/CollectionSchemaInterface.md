# CollectionSchemaInterface


## Namespace
`OpenFGA\Schema`

## Implements
* [SchemaInterface](Schema/SchemaInterface.md)



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

Get the type of each item in the collection.


#### Returns
`string`

### getProperties


```php
public function getProperties(): array<string, SchemaProperty>
```



#### Returns
`array<string, SchemaProperty>`

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

Whether the collection requires at least one item.


#### Returns
`bool`

