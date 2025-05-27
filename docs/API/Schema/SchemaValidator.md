# SchemaValidator


## Namespace
`OpenFGA\Schema`




## Methods
### getSchemas


```php
public function getSchemas(): array<string, SchemaInterface>
```

Get all registered schemas.


#### Returns
`array<string, SchemaInterface>`

### registerSchema


```php
public function registerSchema(SchemaInterface $schema): self
```

Register a schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$schema` | `SchemaInterface` |  |

#### Returns
`self`

### validateAndTransform


```php
public function validateAndTransform(mixed $data, string $className): T
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$data` | `mixed` |  |
| `$className` | `string` |  |

#### Returns
`T`

