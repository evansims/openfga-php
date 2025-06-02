# CollectionSchemaInterface

Interface for collection schema definitions in the OpenFGA system. This interface extends the base SchemaInterface to provide specialized validation and structure definitions for collections of objects. Collection schemas handle arrays and lists of objects that conform to specific types, with support for wrapper keys and item requirements. Collection schemas are essential for validating complex data structures like lists of users, authorization models, relationship tuples, and other grouped data returned by the OpenFGA API. Examples of collections include Users, AuthorizationModels, Tuples, and other array-based response data that require consistent validation and type safety.

## Namespace
`OpenFGA\Schema`

## Implements
* [SchemaInterface](SchemaInterface.md)



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
public function getProperties(): array<string, SchemaProperty>
```

Get all properties defined in this schema. This method returns a comprehensive collection of all properties that make up this schema, including their validation rules, types, and default values. Each property defines how a specific field should be validated and processed.


#### Returns
array&lt;string, SchemaProperty&gt;
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
SchemaProperty&#124;null
 The property definition if it exists, or null if the property is not defined in this schema

### getWrapperKey


```php
public function getWrapperKey(): string|null
```

Get the wrapper key for the collection data if any. Some collections expect data wrapped in a specific key (for example, Usersets uses &#039;child&#039;).


#### Returns
string&#124;null
 The wrapper key or null if data is not wrapped

### requiresItems


```php
public function requiresItems(): bool
```

Whether the collection requires at least one item.


#### Returns
bool
 True if the collection must contain at least one item, false if empty collections are allowed

