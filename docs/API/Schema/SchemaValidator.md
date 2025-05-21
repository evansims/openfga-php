# SchemaValidator


## Namespace
`OpenFGA\Schema`


## Methods
### getSchemas

```php
public function getSchemas(): array
```

Get all registered schemas.


#### Returns
`array` SchemaInterface&gt;

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
public function validateAndTransform(mixed $data, string $className): object
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$data` | `mixed` |  |
| `$className` | `string` |  |

#### Returns
`object` 

