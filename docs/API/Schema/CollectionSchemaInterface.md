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
string

### getItemType


```php
public function getItemType(): string
```

Get the type of each item in the collection.


#### Returns
string

### getProperties


```php
public function getProperties(): array<string, SchemaProperty>
```



#### Returns
array&lt;string, [SchemaProperty](Schema/SchemaProperty.md)&gt;

### getProperty


```php
public function getProperty(string $name): ?OpenFGA\Schema\SchemaProperty
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string |  |

#### Returns
?[SchemaProperty](Schema/SchemaProperty.md)

### getWrapperKey


```php
public function getWrapperKey(): null|string
```

Get the wrapper key for the collection data if any. Some collections expect data wrapped in a specific key (e.g., Usersets uses &#039;child&#039;).


#### Returns
null | string
 The wrapper key or null if data is not wrapped

### requiresItems


```php
public function requiresItems(): bool
```

Whether the collection requires at least one item.


#### Returns
bool

