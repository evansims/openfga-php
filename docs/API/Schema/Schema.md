# Schema

JSON schema definition for validating and transforming data structures. This schema defines validation rules and transformation logic for converting raw API response data into strongly-typed model objects. It specifies property types, validation constraints, and mapping rules for accurate data processing.

## Namespace
`OpenFGA\Schema`

## Implements
* [SchemaInterface](Schema/SchemaInterface.md)



## Methods
### getClassName


```php
public function getClassName(): string
```

Get the fully qualified class name this schema defines. This method returns the class name that this schema describes, which is used during validation and object instantiation to ensure the correct model class is created.


#### Returns
string
 The fully qualified class name for the model this schema defines

### getProperties


```php
public function getProperties(): array
```

Get all properties defined in this schema. This method returns a comprehensive collection of all properties that make up this schema, including their validation rules, types, and default values. Each property defines how a specific field should be validated and processed.


#### Returns
array
 An associative array of property names to their schema property definitions

### getProperty


```php
public function getProperty(string $name): ?OpenFGA\Schema\SchemaProperty
```

Get a specific property definition by name. This method retrieves the schema definition for a particular property, allowing you to access its validation rules, type information, and other metadata for individual fields.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The name of the property to retrieve |

#### Returns
?[SchemaProperty](Schema/SchemaProperty.md)
 The property definition if it exists, or null if the property is not defined in this schema

