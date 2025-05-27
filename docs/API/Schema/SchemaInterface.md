# SchemaInterface


## Namespace
`OpenFGA\Schema`




## Methods
### getClassName


```php
public function getClassName(): string
```



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

