# SchemaProperty

Represents a single property definition within a schema. This class defines the validation rules, type information, and metadata for individual properties of OpenFGA model objects. Each property specifies how a field should be validated, transformed, and mapped during object creation. Properties support various data types including primitives (string, int, bool), complex objects, arrays, and collections, with optional validation constraints such as required status, default values, format restrictions, and enumeration limits.

## Namespace
`OpenFGA\Schema`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php)

## Implements
* [SchemaPropertyInterface](SchemaPropertyInterface.md)



## Methods
### getClassName


```php
public function getClassName(): ?string
```

Get the fully qualified class name for object types.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L53)


#### Returns
?string

### getDefault


```php
public function getDefault(): mixed
```

Get the default value to use when property is missing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L62)


#### Returns
mixed
 Default value for optional properties

### getEnum


```php
public function getEnum(): ?array
```

Get the array of allowed values for enumeration validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L71)


#### Returns
?array
 Array of allowed values or null if not an enumeration

### getFormat


```php
public function getFormat(): ?string
```

Get the additional format constraint for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L80)


#### Returns
?string
 Format constraint (e.g., &#039;date&#039;, &#039;datetime&#039;) or null if none

### getItems


```php
public function getItems(): ?array
```

Get the type specification for array items.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L89)


#### Returns
?array

### getName


```php
public function getName(): string
```

Get the property name as it appears in the data.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L98)


#### Returns
string
 The property name

### getParameterName


```php
public function getParameterName(): ?string
```

Get the alternative parameter name for constructor mapping.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L107)


#### Returns
?string
 Alternative parameter name or null if using default mapping

### getType


```php
public function getType(): string
```

Get the data type for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L116)


#### Returns
string
 The data type (string, integer, boolean, array, object, etc.)

### isRequired


```php
public function isRequired(): bool
```

Check if this property is required for validation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Schema/SchemaProperty.php#L125)


#### Returns
bool
 True if the property is required, false otherwise

