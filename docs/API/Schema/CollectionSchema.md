# CollectionSchema

Schema definition specifically for validating and transforming collection data structures. This specialized schema handles arrays of objects, providing validation rules for collections while ensuring each item conforms to the specified item type. It supports wrapper keys for nested collection data and optional item requirements.

## Namespace
`OpenFGA\Schema`

## Implements
* [CollectionSchemaInterface](Schema/CollectionSchemaInterface.md)
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

### getItemType


```php
public function getItemType(): string
```

Get the type of each item in the collection.


#### Returns
string

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

### getWrapperKey


```php
public function getWrapperKey(): ?string
```

Get the wrapper key for the collection data if any. Some collections expect data wrapped in a specific key (for example, Usersets uses &#039;child&#039;).


#### Returns
?string
 The wrapper key or null if data is not wrapped

### requiresItems


```php
public function requiresItems(): bool
```

Whether the collection requires at least one item.


#### Returns
bool
 True if the collection must contain at least one item, false if empty collections are allowed

