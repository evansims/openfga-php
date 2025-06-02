# SchemaBuilderInterface

Interface for building schema definitions using the builder pattern. This interface provides a fluent API for constructing schema definitions that describe the structure and validation rules for OpenFGA model objects. The builder pattern allows for easy, readable schema creation with method chaining. Schema builders support all common data types including strings, integers, booleans, dates, arrays, and complex objects. Each property can be configured with validation rules such as required status, default values, format constraints, and enumeration restrictions. Example usage: ```php $schema = $builder -&gt;string(&#039;name&#039;, required: true) -&gt;integer(&#039;age&#039;, required: false, default: 0) -&gt;object(&#039;address&#039;, Address::class, required: true) -&gt;register(); ``` The built schemas are automatically registered in the SchemaRegistry for use during validation and object transformation throughout the OpenFGA system.

## Namespace
`OpenFGA\Schema`




## Methods
### array


```php
public function array(string $name, array $items, bool $required = false, mixed $default = NULL): self
```

Add an array property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$items` | array |  |
| `$required` | bool | Whether the property is required |
| `$default` | mixed | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### boolean


```php
public function boolean(string $name, bool $required = false, mixed|null $default = NULL): self
```

Add a boolean property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$default` | mixed|null | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### date


```php
public function date(string $name, bool $required = false, mixed|null $default = NULL): self
```

Add a date property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$default` | mixed|null | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### datetime


```php
public function datetime(string $name, bool $required = false, mixed|null $default = NULL): self
```

Add a datetime property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$default` | mixed|null | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### integer


```php
public function integer(string $name, bool $required = false, mixed|null $default = NULL): self
```

Add an integer property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$default` | mixed|null | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### number


```php
public function number(string $name, bool $required = false, mixed|null $default = NULL): self
```

Add a number (float) property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$default` | mixed|null | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

### object


```php
public function object(string $name, string $className, bool $required = false): self
```

Add an object property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$className` | string |  |
| `$required` | bool | Whether the property is required |

#### Returns
self
 Returns the builder instance for method chaining

### register


```php
public function register(): Schema
```

Build and register the schema. Creates a Schema instance with all defined properties and registers it in the SchemaRegistry for use in validation.


#### Returns
Schema
 The built and registered schema

### string


```php
public function string(string $name, bool $required = false, string|null $format = NULL, array<string>|null $enum = NULL, mixed $default = NULL): self
```

Add a string property to the schema.

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$name` | string | The property name |
| `$required` | bool | Whether the property is required |
| `$format` | string|null | String format constraint (e.g., &#039;date&#039;, &#039;datetime&#039;) |
| `$enum` | array&lt;string&gt;|null | Array of allowed string values |
| `$default` | mixed | Default value for optional properties |

#### Returns
self
 Returns the builder instance for method chaining

