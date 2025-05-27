# SchemaInterface


## Namespace
`OpenFGA\Schema`




## Methods
### getClassName


```php
public function getClassName(): string
```



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

