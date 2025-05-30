# SchemaInterface

Base interface for schema definitions in the OpenFGA system. This interface defines the fundamental contract for all schema objects that describe the structure and validation rules for OpenFGA model classes. Schemas serve as the blueprint for validating raw data (typically from JSON API responses) and transforming it into properly typed PHP objects. Schemas encapsulate the validation rules, type information, and metadata needed to ensure data integrity and type safety throughout the OpenFGA SDK. They define which properties are required, their data types, format constraints, default values, and relationships to other objects. This base interface is extended by specialized schema types such as CollectionSchemaInterface for handling arrays and lists of objects with consistent validation behavior.

## Namespace
`OpenFGA\Schema`




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
public function getProperties(): array<string, SchemaProperty>
```

Get all properties defined in this schema. This method returns a comprehensive collection of all properties that make up this schema, including their validation rules, types, and default values. Each property defines how a specific field should be validated and processed.


#### Returns
array&lt;string, [SchemaProperty](Schema/SchemaProperty.md)&gt;
 An associative array of property names to their schema property definitions

### getProperty


```php
public function getProperty(string $name): SchemaProperty|null
```

Get a specific property definition by name. This method retrieves the schema definition for a particular property, allowing you to access its validation rules, type information, and other metadata for individual fields.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The name of the property to retrieve |

#### Returns
[SchemaProperty](Schema/SchemaProperty.md) | null
 The property definition if it exists, or null if the property is not defined in this schema

