# SchemaRegistry

Centralized registry for managing schema definitions across the OpenFGA system. This registry provides a static, global repository for schema definitions that can be accessed throughout the application lifecycle. It serves as the primary mechanism for storing, retrieving, and creating schema definitions for OpenFGA model objects. The registry supports both programmatic schema creation through the builder pattern and direct schema registration for pre-defined schemas. This centralized approach ensures consistent schema validation across all model objects and eliminates the need for redundant schema definitions. Schemas registered here are used by the SchemaValidator for object validation and transformation during API response processing and data serialization operations.

## Namespace
`OpenFGA\Schema`

## Implements
* [SchemaRegistryInterface](SchemaRegistryInterface.md)



## Methods
### create

*<small>Implements Schema\SchemaRegistryInterface</small>*  

```php
public function create(string $className): SchemaBuilder
```

Create a new schema builder for the specified class.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$className` | string |  |

#### Returns
SchemaBuilder
 A new schema builder instance

### get

*<small>Implements Schema\SchemaRegistryInterface</small>*  

```php
public function get(string $className): ?OpenFGA\Schema\Schema
```

Retrieve a registered schema by class name.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$className` | string |  |

#### Returns
?OpenFGA\Schema\Schema

### register

*<small>Implements Schema\SchemaRegistryInterface</small>*  

```php
public function register(Schema $schema): void
```

Register a schema in the registry.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$schema` | Schema | The schema instance to register |

#### Returns
void

