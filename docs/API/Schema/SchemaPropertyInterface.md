# SchemaPropertyInterface

Interface for schema property definitions. This interface defines the contract for schema property objects that specify validation rules, type information, and metadata for individual properties of OpenFGA model objects. Each property defines how a field should be validated, transformed, and mapped during object creation. Properties support various data types including primitives (string, int, bool), complex objects, arrays, and collections, with optional validation constraints such as required status, default values, format restrictions, and enumeration limits.

## Namespace
`OpenFGA\Schema`




## Methods
### getClassName


```php
public function getClassName(): ?string
```

Get the fully qualified class name for object types.


#### Returns
?string

### getDefault


```php
public function getDefault(): mixed
```

Get the default value to use when property is missing.


#### Returns
mixed
 Default value for optional properties

### getEnum


```php
public function getEnum(): array<string>|null
```

Get the array of allowed values for enumeration validation.


#### Returns
array&lt;string&gt;|null
 Array of allowed values or null if not an enumeration

### getFormat


```php
public function getFormat(): string|null
```

Get the additional format constraint for this property.


#### Returns
string|null
 Format constraint (e.g., &#039;date&#039;, &#039;datetime&#039;) or null if none

### getItems


```php
public function getItems(): ?array
```

Get the type specification for array items.


#### Returns
?array

### getName


```php
public function getName(): string
```

Get the property name as it appears in the data.


#### Returns
string
 The property name

### getParameterName


```php
public function getParameterName(): string|null
```

Get the alternative parameter name for constructor mapping.


#### Returns
string|null
 Alternative parameter name or null if using default mapping

### getType


```php
public function getType(): string
```

Get the data type for this property.


#### Returns
string
 The data type (string, integer, boolean, array, object, etc.)

### isRequired


```php
public function isRequired(): bool
```

Check if this property is required for validation.


#### Returns
bool
 True if the property is required, false otherwise

