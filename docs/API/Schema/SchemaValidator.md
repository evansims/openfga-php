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
array&lt;string, [SchemaInterface](Schema/SchemaInterface.md)&gt;

### registerSchema


```php
public function registerSchema(SchemaInterface $schema): self
```

Register a schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$schema` | [SchemaInterface](Schema/SchemaInterface.md) |  |

#### Returns
self

### validateAndTransform


```php
public function validateAndTransform(mixed $data, string $className): T
```


#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$data` | mixed |  |
| `$className` | string |  |

#### Returns
T

